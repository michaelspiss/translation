<?php

namespace MichaelSpiss\Translation;

interface LoaderInterface {
    /**
     * Returns an array containing key => value pairs of the file.
     * @param string $file
     * @return array
     */
    public function getContent( string $file ): array;
}
