<?php
if (! function_exists('url_mix')) {
    /**
     * Throw an HttpException with the given data.
     *
     * @param  string $path
     * @return string
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function url_mix($path)
    {
        return url(substr(mix($path), 1));
    }
}