<?php

namespace MSansen;

use ActualReports\PDFGeneratorAPI\Client as BaseClient;

/**
 * @author Matthieu Sansen <matthieu.sansen@outlook.com>
 */
class Client extends BaseClient
{
    /**
     * Creates blank template into active workspace and returns template info
     * @param string $name
     *
     * @return \stdClass
     * @throws \ActualReports\PDFGeneratorAPI\Exception
     */
    public function createFromContent(array $content, string $name = null): \stdClass
    {
        if (array_key_exists('id', $content)) {
            unset($content['id']);
        }

        if (null !== $name) {
            $content['name'] = $name;
        }

        $response = $this->request(self::REQUEST_POST, 'templates', [
            'data' => $content,
        ]);

        return $response->response;
    }
}
