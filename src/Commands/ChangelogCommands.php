<?php

namespace EC\OpenEuropa\TaskRunner\Commands;

use Symfony\Component\Console\Input\InputOption;

/**
 * Class ChangelogCommands
 *
 * @package EC\OpenEuropa\TaskRunner\Commands
 */
class ChangelogCommands extends BaseCommands
{
    /**
     * Generate a changelog based on GitHub issues and pull requests.
     *
     * A running Docker installation is required to run this command.
     * For more information check https://github.com/skywinder/github-changelog-generator
     *
     * @command changelog:generate
     *
     * @option token GitHub authentication token, to generate one visit https://github.com/settings/tokens/new
     * @option tag   Specify the future release version you wish to generate the changelog for.
     *
     * @aliases changelog:g,cg
     *
     * @param array $options
     *
     * @return \Robo\Task\Docker\Run
     */
    public function generateChangelog(array $options = [
      'token' => InputOption::VALUE_REQUIRED,
      'tag' => InputOption::VALUE_OPTIONAL,
    ])
    {
        $projectName = $this->getFullProjectName();
        $exec = "{$projectName} -t {$options['token']}";
        if (!empty($options['tag'])) {
            $exec .= " --future-release={$options['tag']}";
        }

        $task = $this->taskDockerRun('muccg/github-changelog-generator')
          ->option('rm')
          ->rawArg('-v $(pwd):$(pwd)')
          ->rawArg('-w $(pwd)')
          ->exec($exec);

        return $task;
    }

    /**
     * Get project name from composer.json.
     *
     * @return string
     *   Project name.
     */
    protected function getFullProjectName()
    {
        $package = json_decode(file_get_contents('./composer.json'));

        return $package->name;
    }
}
