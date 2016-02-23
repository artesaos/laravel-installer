<?php
/**
 * @author    Mauri de Souza Nunes <mauri870@gmail.com>
 * @copyright Copyright (c) 2015, Mauri de Souza Nunes <github.com/mauri870>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace Artesaos\LaravelInstaller\Console\Traits;


use GuzzleHttp\Client;

trait InteractsWithZip
{
    /**
     * Generate a random temporary filename.
     *
     * @return string
     */
    protected function makeFilename()
    {
        return getcwd().'/laravel_'.md5(time().uniqid()).'.zip';
    }


    /**
     * Download the temporary Zip to the given file.
     *
     * @param $zipFile
     * @param $version
     * @return $this
     * @throws \RuntimeException
     */
    protected function download($zipFile, $version)
    {
        try{
            $response = (new Client())->get($this->server_url.$version.".zip");
        } catch(\Exception $e){
            $e->getMessage();
        }

        if(empty($response)){
            throw new \RuntimeException("Sorry! The file can't be downloaded right now. Please try again later");
        }

        file_put_contents($zipFile, $response->getBody());

        return $this;
    }

    /**
     * Extract the zip file into the given directory.
     *
     * @param  string  $zipFile
     * @param  string  $directory
     * @return $this
     */
    protected function extract($zipFile, $directory)
    {
        $archive = new \ZipArchive();

        $archive->open($zipFile);

        $archive->extractTo($directory);

        $archive->close();

        return $this;
    }

    /**
     * Clean-up the Zip file.
     *
     * @param  string  $zipFile
     * @return $this
     */
    protected function cleanUp($zipFile)
    {
        @chmod($zipFile, 0777);

        @unlink($zipFile);

        return $this;
    }
}