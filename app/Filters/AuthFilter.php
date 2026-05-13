<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Veuillez vous connecter.');
        }

        if (! empty($arguments)) {
            $allowed = [];
            if (is_array($arguments)) {
                $allowed = explode('|', (string) ($arguments[0] ?? ''));
            } else {
                $allowed = explode('|', (string) $arguments);
            }

            $userRole = $session->get('role');
            if (! $userRole || ! in_array($userRole, $allowed, true)) {
                return redirect()->to(site_url('login'))->with('error', 'Accès non autorisé.');
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
