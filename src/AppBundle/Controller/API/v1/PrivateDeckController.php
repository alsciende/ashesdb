<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Deck;
use AppBundle\Manager\DeckManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Private decks
 *
 * @author Cédric Bertolini <cedric.bertolini@proximedia.fr>
 */
class PrivateDeckController extends BaseApiController
{

    /**
     * Create a new private deck in version 0.1
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Private Decks",
     * )
     * @Route("/decks")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Request $request)
    {
        $data = json_decode($request->getContent(), TRUE);
        
        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $deck = $manager->create($data, $this->getUser());
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($deck);
    }

    /**
     * Get all private decks
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Private Decks",
     * )
     * @Route("/decks")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction ()
    {
        $decks = $this->getDoctrine()->getRepository(Deck::class)->findBy(['user' => $this->getUser()]);
        return $this->success($decks);
    }

    /**
     * Get a private deck
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Private Decks",
     * )
     * @Route("/decks/{id}")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function getAction (Deck $deck)
    {
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        return $this->success($deck);
    }

    /**
     * Update a deck, increasing its minorVersion
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Private Decks",
     * )
     * @Route("/decks/{id}")
     * @Method("PUT")
     * @Security("has_role('ROLE_USER')")
     */
    public function putAction (Request $request, Deck $deck)
    {
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $data = json_decode($request->getContent(), TRUE);
        
        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $updated = $manager->update($data, $deck);
        } catch (Exception $ex) {
            throw $ex;
            return $this->failure($ex->getMessage());
        }

        return $this->success($updated);
    }

}