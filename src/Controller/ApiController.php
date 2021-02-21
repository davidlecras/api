<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/liste-departements-par-regions", name="list_deps_regions")
     */
    public function list_deps_regions(Request $request, SerializerInterface $serializerInterface): Response
    {
        $coedRegion = $request->query->get('region');
        // Récupération des regions:
        $regions = file_get_contents('https://geo.api.gouv.fr/regions');
        $regions_decode = $serializerInterface->deserialize($regions, 'App\Entity\Regions[]', 'json');
        if ($coedRegion == null || $coedRegion == 'Toutes') {
            $departements = file_get_contents('https://geo.api.gouv.fr/departements');
        } else {
            $departements = file_get_contents("https://geo.api.gouv.fr/regions/$coedRegion/departements");
        }

        //decode Json en tableaux:
        $departements = $serializerInterface->decode($departements, 'json');

        return $this->render('api/departements_par_region.html.twig', [
            'regions' => $regions_decode,
            'deps' => $departements
        ]);
    }
}
