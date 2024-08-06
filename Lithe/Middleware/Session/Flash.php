<?php

namespace Lithe\Middleware\Session;

/**
 * Middleware that provides flash message functionality for the session.
 *
 * @return \Closure Middleware function that handles flash messages.
 */

function flash()
{
    /**
     *
     * @param \Lithe\Contracts\Http\Request $req
     * @param \Lithe\Contracts\Http\Response $res
     * @param callable   $next
     */
    return function (\Lithe\Contracts\Http\Request $req, \Lithe\Contracts\Http\Response $res, $next) {
        $req->flash = new \Lithe\Support\Session\Flash;
        $next();
    };
}
