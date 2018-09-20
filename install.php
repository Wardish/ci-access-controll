<?php
$installer = new Installer();
$installer->install();
class Installer
{
    const TEST_FOLDER = 'tests';
    public function install($app = 'application')
    {
        $this->recursiveCopy(
            dirname(__FILE__) . '/application', $app
        );
    }
    /**
     * Recursive Copy
     *
     * @param string $src
     * @param string $dst
     */
    private function recursiveCopy($src, $dst)
    {
        @mkdir($dst, 0755);
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                @mkdir($dst . '/' . $iterator->getSubPathName());
            } else {
                $success = copy($file, $dst . '/' . $iterator->getSubPathName());
                if ($success) {
                    if (! $this->silent) {
                        echo 'copied: ' . $dst . '/' . $iterator->getSubPathName() . PHP_EOL;
                    }
                }
            }
        }
    }
    /**
     * Recursive Unlink
     *
     * @param string $dir
     */
    private function recursiveUnlink($dir)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file);
            } else {
                unlink($file);
            }
        }
        
        rmdir($dir);
    }
}
