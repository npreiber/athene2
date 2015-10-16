<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Alias\Controller;

use Alias\AliasManagerInterface;
use Entity\Manager\EntityManagerInterface;
use Normalizer\NormalizerInterface;
use Taxonomy\Manager\TaxonomyManagerInterface;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Console\Console;
use Zend\Mvc\Controller\AbstractConsoleController;

/**
 * A controller for controlling the index.
 */
class RefreshController extends AbstractConsoleController
{
    /**
     * @var AliasManagerInterface
     */
    protected $aliasManager;

    /**
     * @var TaxonomyManagerInterface
     */
    protected $taxonomyManager;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @param AliasManagerInterface $aliasManager
     * @param TaxonomyManagerInterface $taxonomyManager
     * @param EntityManagerInterface $entityManager
     * @param NormalizerInterface $normalizer
     */
    public function __construct(
        AliasManagerInterface $aliasManager,
        TaxonomyManagerInterface $taxonomyManager,
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer
    )
    {
        $this->aliasManager = $aliasManager;
        $this->taxonomyManager = $taxonomyManager;
        $this->entityManager = $entityManager;
        $this->normalizer = $normalizer;
    }

    public function refreshAction()
    {
        $console = $this->getServiceLocator()->get('console');
        $percentile = $this->params()->fromRoute('percentile', 10);
        $skipTerms = $this->params()->fromRoute('skip-terms', false);
        $skipEntities = $this->params()->fromRoute('skip-entities', false);

        if (!$skipTerms) {
            $this->refreshTerms($console, $percentile);
        }
        if (!$skipEntities) {
            $this->refreshEntities($console, $percentile);
        }

        $this->aliasManager->flush();
        return "All done!\n";
    }

    protected function refreshTerms(AdapterInterface $console, $percentile)
    {
        $terms = $this->taxonomyManager->findAllTerms(true);
        foreach ($terms as $term) {
            if (rand(0, 100) > $percentile) {
                $console->writeLine('Left out taxonomy term ' . $term->getName() . ' (' . $term->getId() . ')');
                continue;
            }
            $instance = $term->getInstance();
            $normalizer = $this->normalizer->normalize($term);
            $routeName = $normalizer->getRouteName();
            $routeParams = $normalizer->getRouteParams();
            $router = $this->aliasManager->getRouter();
            $url = $router->assemble($routeParams, ['name' => $routeName]);
            $this->aliasManager->autoAlias('taxonomyTerm', $url, $term, $instance);
            $console->writeLine('Updated taxonomy term ' . $term->getName() . ' (' . $term->getId() . ')');
        }
    }

    protected function refreshEntities(AdapterInterface $console, $percentile) {
        $entities = $this->entityManager->findAll(true);
        foreach ($entities as $entity) {
            if (rand(0, 100) > $percentile) {
                $console->writeLine('Left out entity ' . $entity->getId() . '');
                continue;
            }
            $instance = $entity->getInstance();
            $url = $this->aliasManager->getRouter()->assemble(
                ['entity' => $entity->getId()],
                ['name' => 'entity/page']
            );
            $this->aliasManager->autoAlias('entity', $url, $entity, $instance);
            $console->writeLine('Updated entity ' . $entity->getId() . '');
        }

    }
}
