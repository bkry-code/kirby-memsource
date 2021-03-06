<?php

if (function_exists('panel')) {
    kirby()->set('widget', 'memsource', __DIR__ . DS . 'widgets' . DS . 'memsource');

    require_once(__DIR__ . DS . 'src' . DS . 'BlueprintReader.php');
    require_once(__DIR__ . DS . 'src' . DS . 'Exporter.php');
    require_once(__DIR__ . DS . 'src' . DS . 'Importer.php');
    require_once(__DIR__ . DS . 'src' . DS . 'KirbytagXML.php');

    panel()->routes([
        [
            'pattern' => 'memsource/export',
            'method' => 'GET',
            'action' => function () {
                $exporter = new Memsource\Exporter;
                $content = $exporter->export();

                $data = [
                    'content' => $content
                ];

                if (count($exporter->alerts) > 0) {
                    $data['alerts'] = $exporter->alerts;
                }

                return response::json($data);
            }
        ],
        [
            'pattern' => 'memsource/import',
            'method' => 'PUT',
            'action' => function () {
                $postData = file_get_contents('php://input');
                $input = json_decode($postData, true);

                if (!$input) {
                    return response::json([
                        'status' => 'error',
                        'errorDescription' => 'Could not parse data.'
                    ], 400);
                } else if (empty($input['data']) || empty($input['language'])) {
                    return response::json([
                        'status' => 'error',
                        'errorDescription' => 'Missing input data.'
                    ], 400);
                }

                $importer = new Memsource\Importer;
                $importer->import($input['data'], $input['language']);

                return response::json([
                    'status' => 'success'
                ]);
            }
        ]
    ]);
}
