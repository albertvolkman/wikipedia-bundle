<?php
/**
 * @file
 * Contains AlbertVolkman\Wikipedia\Command\WikipediaCommand.
 */

namespace AlbertVolkman\Wikipedia\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;

class WikipediaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
          ->setName('wikipedia:search')
          ->setDescription('Search for Infoboxes on Wikipedia');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        $container = $this->getContainer();
        $helper_parser = $container->get('wikipedia.helper_parser');
        $action_query = $container->get('wikipedia.action_query');

        // Find a valid Wikipedia article.
        $wiki_term = $dialog->ask(
          $output,
          'Please enter search term: ',
          null,
          function ($wiki_term) {
              $search_options = $this
                  ->getContainer()
                  ->get('wikipedia.action_opensearch')
                  ->search($wiki_term);

              if (!empty($search_options[1])) {
                  return $search_options[1];
              }

              return array();
          }
        );

        // Retrieve the resultant page ID.
        $page_ids = $action_query->pageIds(array($wiki_term));
        $page_id = $page_ids['query']['pageids'][0];

        // Find the infobox for the requested term.
        $info_box = $action_query->infoBox(array($wiki_term));
        $info_box = $info_box['query']['pages'][$page_id]['revisions'][0]['*'];
        $info_box = $helper_parser->infoBox($info_box);

        // Output results.
        print "\n";
        foreach ($info_box as $key => $data) {
            print "$key: $data\n";
        }
        print "\n";
    }
}
