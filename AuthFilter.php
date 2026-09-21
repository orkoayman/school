<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * $arguments: optional list of allowed roles for this route,
     * e.g. filter set to 'auth:admin' only allows admin role.
     * No arguments = any logged-in user (admin or teacher) allowed.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'অনুগ্রহ করে লগইন করুন।');
        }

        if (! empty($arguments)) {
            $userRole = $session->get('role');
            if (! in_array($userRole, $arguments, true)) {
                return redirect()->to('/dashboard')->with('error', 'আপনার এই পাতায় প্রবেশের অনুমতি নেই।');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after the request.
    }
}
