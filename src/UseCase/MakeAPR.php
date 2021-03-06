<?php

namespace PUGX\Bot\UseCase;

use PUGX\Bot\LocalPackage;
use PUGX\Bot\Message\MessageRepositoryInterface;
use Github\Client;

class MakeAPR
{
    const PREFIX = <<< EOF
| Q             | A
| ------------- | ---
| Bug fix?      | no
| New feature?  | no
| BC breaks?    | no
| Deprecations? | no
| Tests pass?   | yes
| Fixed tickets |
| License       | MIT
| Doc PR        |
EOF;

    private $messageRepository;
    private $client;

    function __construct(Client $client, MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->client = $client;
    }

    public function execute(LocalPackage $package)
    {
        $message = (string)$this->messageRepository->fetch();
        $pullRequest = $this->client
            ->api('pull_request')
            ->create( $package->getUsername(), $package->getRepoName(), array(
                'base' => 'master',
                'head' => 'master',
                'title' => 'CS Fixes',
                'body' => $message
            ));
        return 201 === $this->client->getHttpClient()->getLastResponse()->getStatusCode();
    }

    private function getPrefixCommitMessage()
    {
        return self::PREFIX;
    }

    private function getCommitTitle()
    {
        return '[CS] Coding Standard fixed';
    }
} 