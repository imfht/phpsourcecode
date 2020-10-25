<?php

namespace BitterGourd\Command;

use BitterGourd\NodeVisitor\ForeachNodeVisitor;
use BitterGourd\NodeVisitor\ForNodeVisitor;
use BitterGourd\NodeVisitor\FunctionNodeVisitor;
use BitterGourd\NodeVisitor\IfNodeVisitor;
use BitterGourd\NodeVisitor\MethodCallNodeVisitor;
use BitterGourd\NodeVisitor\PropertyFetchNodeVisitor;
use BitterGourd\NodeVisitor\StringNodeVisitor;
use BitterGourd\NodeVisitor\SwitchNodeVisitor;
use BitterGourd\NodeVisitor\VariableNodeVisitor;
use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use PhpParser\PrettyPrinter;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RunCommand extends Command
{

    protected static $defaultName = 'run';

    protected function configure()
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Who do you want to greet?')
            ->setDescription('Describe args behaviors')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('path', 'p', InputArgument::OPTIONAL, 'Select a directory.'),
                    new InputOption('file', 'f', InputArgument::OPTIONAL, 'Select a file.'),
                ])
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new Filesystem();
        $finder = new Finder();

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Have you backed up your files or directories? (y):', true);

        if (!$helper->ask($input, $output, $question)) {
            return 0;
        }

        $path = $input->getOption('path');
        $file = $input->getOption('file');
        $phpFiles = [];

        if ($path != null && $filesystem->exists($path)) {
            $phpFiles = iterator_to_array($finder->in($path)->name('*.php')->files());
        }

        if (count($phpFiles) <= 0) {
            if ($file != null && $filesystem->exists($file)) {
                $phpFiles = [$file];
            }
        }

        foreach ($phpFiles as $phpFile) {
            $output->writeln($phpFile);
            $code = trim(file_get_contents($phpFile));
            $newCode = $this->obscure($code);
            file_put_contents($phpFile, $newCode);
        }

        $output->writeln('done.');
        return 0;
    }

    private function obscure($code)
    {

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            return $code;
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new ForeachNodeVisitor());
        $traverser->addVisitor(new ForNodeVisitor());
        $traverser->addVisitor(new IfNodeVisitor());
        $traverser->addVisitor(new SwitchNodeVisitor());
        $traverser->addVisitor(new MethodCallNodeVisitor());
        $traverser->addVisitor(new PropertyFetchNodeVisitor());
        $traverser->addVisitor(new FunctionNodeVisitor());
        $traverser->addVisitor(new StringNodeVisitor());
        $traverser->addVisitor(new VariableNodeVisitor());
        ////$traverser->addVisitor(new LineNodeVisitor());
        $ast = $traverser->traverse($ast);

        $prettyPrinter = new PrettyPrinter\Standard;
        return $prettyPrinter->prettyPrintFile($ast);
    }


}
