<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    //Utiliser une API mis a dispo:

    /**
     * @Route("/liste-regions", name="list_regions")
     */
    public function list_regions(SerializerInterface $serializerInterface): Response
    {
        $regions = file_get_contents('https://geo.api.gouv.fr/regions');
        //déserialisation des donnees par étapes:
        // $regions_array = $serializerInterface->decode($regions, 'json');
        // Pas d'appel de la BDD car non migré:
        // $regions_object = $serializerInterface->denormalize($regions_array, 'App\Entity\Regions[]');

        // désérialization avec la methode deserialize:
        $regions_decode = $serializerInterface->deserialize($regions, 'App\Entity\Regions[]', 'json');
        return $this->render('api/index.html.twig', [
            'regions' => $regions_decode
        ]);
    }
}
