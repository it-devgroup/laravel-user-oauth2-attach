<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Console\Commands;

use Carbon\Carbon;
use Illuminate\Support\Str;
use ItDevgroup\LaravelUserOAuth2Attach\Providers\UserOAuth2AttachServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

/**
 * Class UserOAuth2AttachPublishCommand
 * @package ItDevgroup\LaravelUserOAuth2Attach\Console\Commands
 */
class UserOAuth2AttachPublishCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'user:oauth2:attach:publish {--tag=* : Tag for published}';
    /**
     * @var string
     */
    protected $description = 'Publish files for user OAuth2 attach package';
    /**
     * @var array
     */
    private array $files = [];
    /**
     * @var array
     */
    private array $fileTags = [
        'config',
    ];

    /**
     * @return void
     */
    public function handle()
    {
        $option = is_array($this->option('tag')) && !empty($this->option('tag')) ? $this->option('tag')[0] : '';

        $this->parsePublishedFiles();

        switch ($option) {
            case 'config':
                $this->copyConfig();
                break;
            case 'migration':
                $this->copyMigration();
                break;
            default:
                $this->error('Not selected tag');
                break;
        }
    }

    /**
     * @return void
     */
    private function parsePublishedFiles(): void
    {
        $index = 0;
        foreach (UserOAuth2AttachServiceProvider::pathsToPublish(UserOAuth2AttachServiceProvider::class) as $k => $v) {
            $this->files[$this->fileTags[$index]] = [
                'from' => $k,
                'to' => $v,
            ];
            $index++;
        }
    }

    /**
     * @return void
     */
    private function copyConfig(): void
    {
        $this->copyFiles($this->files['config']['from'], $this->files['config']['to']);
    }

    /**
     * @return void
     */
    private function copyMigration(): void
    {
        $newFileName = sprintf(
            '%s_create_%s.php',
            Carbon::now()->format('Y_m_d_His'),
            Config::get('user_oauth2_attach.table')
        );

        copy(
            __DIR__ . '/../../../migration/migration.stub',
            base_path('database/migrations/' . $newFileName)
        );

        $this->parseContent(base_path('database/migrations/' . $newFileName));

        $this->info(
            sprintf(
                'File "%s" created',
                '/database/migrations/' . $newFileName
            )
        );
    }

    /**
     * @param string $from
     * @param string $to
     */
    private function copyFiles(string $from, string $to): void
    {
        if (!file_exists($to)) {
            mkdir($to, 0755, true);
        }
        $from = rtrim($from, '/') . '/';
        $to = rtrim($to, '/') . '/';
        foreach (scandir($from) as $file) {
            if (!is_file($from . $file)) {
                continue;
            }

            $path = strtr(
                $to . $file,
                [
                    base_path() => ''
                ]
            );

            if (file_exists($to . $file)) {
                $this->info(
                    sprintf(
                        'File "%s" skipped',
                        $path
                    )
                );
                continue;
            }

            copy(
                $from . $file,
                $to . $file
            );

            $this->parseContent($to . $file);

            $this->info(
                sprintf(
                    'File "%s" copied',
                    $path
                )
            );
        }
    }

    /**
     * @param string $file
     */
    private function parseContent(string $file)
    {
        $content = file_get_contents($file);
        $content = strtr($content, ['{{TABLE_NAME}}' => Config::get('user_oauth2_attach.table')]);
        $content = strtr(
            $content,
            [
                '{{MIGRATION_NAME}}' => Str::ucfirst(
                    Str::camel(
                        Config::get('user_oauth2_attach.table')
                    )
                )
            ]
        );
        file_put_contents($file, $content);
    }
}
