<?php

declare(strict_types=1);

namespace Salette\Helpers;

use Salette\Exceptions\DirectoryNotFoundException;
use Salette\Exceptions\UnableToCreateDirectoryException;
use Salette\Exceptions\UnableToCreateFileException;

class Storage
{
    /**
     * The base directory to access the files.
     */
    protected string $baseDirectory;

    /**
     * @throws DirectoryNotFoundException|UnableToCreateDirectoryException
     */
    public function __construct(string $baseDirectory, bool $createMissingBaseDirectory = false)
    {
        if (! is_dir($baseDirectory)) {
            if ($createMissingBaseDirectory) {
                $this->createDirectory($baseDirectory);
            } else {
                throw new DirectoryNotFoundException($baseDirectory);
            }
        }

        $this->baseDirectory = $baseDirectory;
    }

    /**
     * Get the base directory
     */
    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }

    /**
     * Combine the base directory with a path.
     */
    protected function buildPath(string $path): string
    {
        $trimRules = DIRECTORY_SEPARATOR . ' ';

        return rtrim($this->baseDirectory, $trimRules) . DIRECTORY_SEPARATOR . ltrim($path, $trimRules);
    }

    /**
     * Check if the file exists
     */
    public function exists(string $path): bool
    {
        return file_exists($this->buildPath($path));
    }

    /**
     * Check if the file is missing
     */
    public function missing(string $path): bool
    {
        return ! $this->exists($path);
    }

    /**
     * Retrieve an item from storage
     */
    public function get(string $path)
    {
        return file_get_contents($this->buildPath($path));
    }

    /**
     * Put an item in storage
     *
     * @return $this
     *
     * @throws UnableToCreateDirectoryException|UnableToCreateFileException
     */
    public function put(string $path, string $contents): self
    {
        $fullPath = $this->buildPath($path);

        $directoryWithoutFilename = implode(DIRECTORY_SEPARATOR, explode(DIRECTORY_SEPARATOR, $fullPath, -1));

        if (empty($directoryWithoutFilename) === false && is_dir($directoryWithoutFilename) === false) {
            $this->createDirectory($directoryWithoutFilename);
        }

        $createdFile = file_put_contents($fullPath, $contents);

        if ($createdFile === false) {
            throw new UnableToCreateFileException($fullPath);
        }

        return $this;
    }

    /**
     * Create a directory
     *
     * @throws UnableToCreateDirectoryException
     */
    public function createDirectory(string $directory): bool
    {
        $createdDirectory = mkdir($directory, 0777, true);

        if ($createdDirectory === false && is_dir($directory) === false) {
            throw new UnableToCreateDirectoryException($directory);
        }

        return true;
    }
}
