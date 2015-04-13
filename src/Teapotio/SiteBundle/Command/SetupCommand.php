<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    SiteBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\SiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;

/**
 * Install the basic roles etc
 */
class SetupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputOption('username', '', InputOption::VALUE_REQUIRED, "The admin's username"),
                new InputOption('email', '', InputOption::VALUE_REQUIRED, "The admin's email"),
                new InputOption('password', '', InputOption::VALUE_REQUIRED, "The admin's password"),
                new InputOption('default-topic-board', '', InputOption::VALUE_REQUIRED, "Whether the command should create a default board and a default topic"),
            ))
            ->setName('teapotio:forum:install')
            ->setDescription('Install the necessary teapotio components')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion($questionHelper->getQuestion('Do you confirm setup', 'yes', '?'));
            if (!$questionHelper->ask($input, $output, $question, true)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        foreach (array('username', 'email', 'password') as $option) {
            if (null === $input->getOption($option)) {
                throw new \RuntimeException(sprintf('The "%s" option must be provided.', $option));
            }
        }

        $output->writeln('Installing <comment>components</comment>.');

        $output->writeln('Setting up default images.');
        $images = $this->getContainer()->get('teapotio.image')->setup();

        $output->writeln('Setting up default groups.');
        $groups = $this->getContainer()->get('teapotio.user.group')->setup();

        $output->writeln('Setting up default user.');
        $username = $input->getOption('username');
        $email = $input->getOption('email');
        $password = $input->getOption('password');

        $users = $this->getContainer()->get('teapotio.user')->setup($username, $email, $password, $groups, $images);

        if ($input->getOption('default-topic-board')) {
            $output->writeln('Setting up default board and topic.');

            $boards = $this->getContainer()->get('teapotio.forum.board')->setup($users[0]);

            $topic = $this->getContainer()->get('teapotio.forum.topic')->setup($users[0], $boards[0]);
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Installing Teapotio Forum');

        // username
        $username = null;
        try {
            $username = $input->getOption('username');
        } catch (\Exception $error) {
            $output->writeln($questionHelper->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (null === $username) {
            $output->writeln(array(
                '',
                'You can use this admin account as yours.',
            ));

            $question = new Question($questionHelper->getQuestion("Admin's username", $input->getOption('username')));
            $username = $questionHelper->ask($input, $output, $question);
            $input->setOption('username', $username);
        }

        // email
        $email = null;
        try {
            $email = $input->getOption('email');
        } catch (\Exception $error) {
            $output->writeln($questionHelper->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (null === $email) {
            $output->writeln(array(
                '',
                'Please specify a valid email address.',
            ));

            $question = new Question($questionHelper->getQuestion("Admin's email", $input->getOption('email')));
            $email = $questionHelper->ask($input, $output, $question);
            $input->setOption('email', $email);
        }

        // password
        $password = null;
        try {
            $password = strlen($input->getOption('password')) > 8 ? $input->getOption('password') : null;
        } catch (\Exception $error) {
            $output->writeln($questionHelper->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (null === $password) {
            $output->writeln(array(
                '',
                'We recommend a password of length above 20 characters.',
            ));

            $question = new Question($questionHelper->getQuestion("Admin's password", $input->getOption('password')));
            $password = $questionHelper->ask($input, $output, $question);
            $input->setOption('password', $password);
        }

        $defaultTopicBoard = $input->getOption('default-topic-board');
        $question = new ConfirmationQuestion($questionHelper->getQuestion('Do you want to generate a default board and a default topic', 'yes', '?'));
        if (!$defaultTopicBoard && $questionHelper->ask($input, $output, $question, true)) {
            $defaultTopicBoard = true;
        } else {
            $defaultTopicBoard = false;
        }

        $input->setOption('default-topic-board', $defaultTopicBoard);
    }

    protected function getQuestionHelper()
    {
        $question = $this->getHelperSet()->get('question');
        if (!$question || get_class($question) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper') {
            $this->getHelperSet()->set($question = new QuestionHelper());
        }

        return $question;
    }
}
