<?php

namespace App;

class Rest {
    public static function open($server) {
        // Get the full request URL
        $url = explode('/', $server['REQUEST_URI']);

        // Clean the URL: remove empty or null elements
        $url = array_filter($url, fn($a) => trim($a) !== "");

        // Remove the initial parts of the URL not related to application logic
        $index = array_search('api', $url);
        if ($index === false) {
            echo json_encode(['status' => 'error', 'code' => 400, 'data' => 'Invalid API route']);
            return;
        }

        // Check that the URL has enough parts for module, controller, and action
        array_shift($url);

        if (count($url) < 3) {
            echo json_encode(['status' => 'error', 'code' => 400, 'data' => 'Insufficient URL parts']);
            return;
        }

        $module = ucfirst(strtolower($url[0]));
        $controller = ucfirst(strtolower($url[1]));
        $action = strtolower($url[2]);
        $param = $url[3] ?? null;

        // Security: validate controller name
        if (!preg_match('/^[A-Za-z0-9_]+$/', $controller)) {
            echo json_encode(['status' => 'error', 'code' => 400, 'data' => 'Invalid controller name']);
            return;
        }

        // Build the controller class path inside the module
        $controllerClass = "modules\\$module\\controller\\{$controller}Controller";

        $headers = getallheaders();
        $token = null;

        // Extract Bearer token from Authorization header
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                $token = $matches[1];
            }
        }

        // Check if the controller class and method exist
        if (class_exists($controllerClass) && method_exists($controllerClass, $action)) {
            $requestData = array_merge($_GET, $_POST);
            if (!empty($param)) {
                $requestData['param'] = $param;
            }

            if (!empty($token)) {
                $requestData['token'] = $token;
            }

            try {
                $data = call_user_func_array([new $controllerClass, $action], [$requestData]);

                if (is_array($data) && isset($data['error'])) {
                    throw new \Exception($data['error'], $data['code'] ?? 400);
                }

                echo json_encode([
                    'status' => 'success',
                    'code' => 200,
                    'data' => $data
                ]);
            } catch (\Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'code' => $e->getCode() ?: 500,
                    'data' => $e->getMessage()
                ]);
            }
        } else {
            echo json_encode(['status' => 'error', 'code' => 404, 'data' => 'Method or class not found!']);
        }
    }
}
