<?php

namespace Lithe\Component;

use Exception;
use Lithe\Support\Log;

/**
 * Class Upload
 *
 * A simple class for handling file uploads.
 */
class Upload
{
    /**
     * @var array The file data from the upload.
     */
    private $file;

    /**
     * Upload constructor.
     *
     * @param array $file The file data from the upload ($_FILES array).
     */
    public function __construct(array $file)
    {
        $this->file = $file;
    }

    /**
     * Moves the uploaded file to a specified directory, generating a unique filename.
     *
     * @param string $uploadDir The directory where the file will be moved to.
     * @param array|null $allowedExtensions An array of allowed file extensions.
     *
     * @return string|null The path of the moved file on success, otherwise null.
     * @throws Exception If there are issues during the move operation.
     */
    public function move(string $uploadDir, ?array $allowedExtensions = null): ?string
    {
        try {
            // Validate that the file array is properly structured
            $this->validateFileArray();

            // Ensure the upload directory exists and is writable
            $this->validateUploadDir($uploadDir);

            // Validate file extension if allowed extensions are provided
            if ($allowedExtensions !== null) {
                $this->validateFileExtension($allowedExtensions);
            }

            // Generate a unique filename to avoid overwriting
            $uniqueFilename = $this->generateUniqueFilename();
            $filePath = "$uploadDir/$uniqueFilename";

            // Attempt to move the file to the destination directory
            if (move_uploaded_file($this->file['tmp_name'], $filePath)) {
                return $filePath;
            } else {
                throw new Exception('Failed to move the file to the destination directory.');
            }
        } catch (Exception $e) {
            // Log the error for future analysis
            error_log($e->getMessage());
            Log::error($e);
            return null;
        }
    }

    /**
     * Validates the structure of the file array.
     *
     * @throws Exception If the file array is invalid.
     */
    private function validateFileArray(): void
    {
        if (!is_array($this->file) || !isset($this->file['error'])) {
            throw new Exception('Invalid file upload array.');
        }
        if ($this->file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error uploading file: ' . $this->file['error']);
        }
    }

    /**
     * Validates the upload directory.
     *
     * @param string $uploadDir The directory to validate.
     *
     * @throws Exception If the directory is invalid.
     */
    private function validateUploadDir(string $uploadDir): void
    {
        if (!is_dir($uploadDir)) {
            throw new Exception('Upload directory does not exist.');
        }
        if (!is_writable($uploadDir)) {
            throw new Exception('Upload directory is not writable.');
        }
    }

    /**
     * Validates the file extension against allowed extensions.
     *
     * @param array $allowedExtensions An array of allowed file extensions.
     *
     * @throws Exception If the file extension is not allowed.
     */
    private function validateFileExtension(array $allowedExtensions): void
    {
        $fileExtension = strtolower(pathinfo($this->file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('File extension not allowed.');
        }
    }

    /**
     * Generates a unique filename based on the original file name.
     *
     * @return string The unique filename.
     */
    private function generateUniqueFilename(): string
    {
        return md5(uniqid('', true)) . '_' . $this->file['name'];
    }

    /**
     * Checks if a file is uploaded.
     *
     * @return bool True if the file is uploaded, otherwise false.
     */
    public function isUploaded(): bool
    {
        return isset($this->file['error']) && $this->file['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Retrieves the MIME type of the uploaded file.
     *
     * @return string|null The MIME type of the file, or null if not available.
     */
    public function getMimeType(): ?string
    {
        return $this->file['type'] ?? null;
    }

    /**
     * Retrieves the size of the uploaded file.
     *
     * @return int|null The size of the file in bytes, or null if not available.
     */
    public function getSize(): ?int
    {
        return $this->file['size'] ?? null;
    }
}
