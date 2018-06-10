<?php

namespace EspritEntreAide\ClubBundle\Controller;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use EspritEntreAide\ClubBundle\ClubBundle;
use EspritEntreAide\ClubBundle\Entity\Club;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $club = $em->getRepository("ClubBundle:Club")->findAll();

        $pieChart = new PieChart();
        $em = $this->getDoctrine();
        $clubs = $em->getRepository(Club::class)->findAll();
        $totalmembre = 0;

        foreach ($clubs as $clb) {
            $totalmembre = $totalmembre + $clb->getNbmemb();
        }
        $data = array();
        $stat = ['Club', 'nbmemb'];
        $nb = 0;
        array_push($data, $stat);
        foreach ($clubs as $clb) {
            $stat = array();
            array_push($stat, $clb->getNomC(), (($clb->getNbmemb()) * 100) / $totalmembre);
            $nb = ($clb->getNbmemb() * 100) / $totalmembre;
            $stat = [$clb->getNomC(), $nb];
            array_push($data, $stat);
        }
        $pieChart->getData()->setArrayToDataTable($data);
        $pieChart->getOptions()->setTitle('Pourcentages des Membres par Club');
        $pieChart->getOptions()->setHeight(400);
        $pieChart->getOptions()->setWidth(650);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);


        return $this->render('ClubBundle:Default:index.html.twig', array(
            "cl" => $club, 'piechart' => $pieChart));
    }

}
