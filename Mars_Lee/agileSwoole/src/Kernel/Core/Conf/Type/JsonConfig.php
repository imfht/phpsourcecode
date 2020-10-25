<?php

namespace Kernel\Core\Conf\Type;


use Kernel\Core\Conf\IConfig;

class JsonConfig implements IConfig
{
        public function load(string $filename) : array
        {
                $config = $this->parseJson($filename);

                if (JSON_ERROR_NONE !== json_last_error()) {
                        $jsonError = $this->getJsonError(json_last_error());
                        throw new \RuntimeException(
                                sprintf('Invalid JSON provided "%s" in "%s"', $jsonError, $filename));
                }

                return $config ?: [];
        }

        public function supports(string $filename) : bool
        {
                return (bool) preg_match('#\.json(\.dist)?$#', $filename);
        }

        private function parseJson($filename)
        {
                $json = file_get_contents($filename);
                return json_decode($json, true);
        }

        private function getJsonError($code)
        {
                $errorMessages = array(
                        JSON_ERROR_DEPTH            => 'The maximum stack depth has been exceeded',
                        JSON_ERROR_STATE_MISMATCH   => 'Invalid or malformed JSON',
                        JSON_ERROR_CTRL_CHAR        => 'Control character error, possibly incorrectly encoded',
                        JSON_ERROR_SYNTAX           => 'Syntax error',
                        JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded',
                );

                return isset($errorMessages[$code]) ? $errorMessages[$code] : 'Unknown';
        }
}
