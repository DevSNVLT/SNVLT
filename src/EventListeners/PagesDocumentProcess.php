<?php

namespace App\EventListeners;

use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\References\PageDocGen;
use App\Events\DocPages\AddPagesCpEvent;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PagesDocumentProcess
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private LoggerInterface $logger,
        private TranslatorInterface $translator)
    {
    }

    public function onPagesCpAdd(AddPagesCpEvent $event)
    {
        //Génétion des pages du document CP

        $pages_doc_stock = $this->doctrine->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$event->getDocumentCp()]);

        foreach ($pages_doc_stock as $page){
            $page_cp = new Pagecp();
            $page_cp->setUniqueDoc($event->getDocumentCp()->getUniqueDoc().$page->getNumpage());
            $page_cp->setCodeDoccp($event->getDocumentCp());
            $page_cp->setNumeroPagecp($page->getNumeroPage());
            $page_cp->setCreatedAt(new \DateTime());
            $page_cp->setCreatedBy($event->getDocumentCp()->getCreatedBy());
            $page_cp->setIndex($page->getNumeroPage());
            $page_cp->setFini(false);

            $this->doctrine->getManager()->persist($page_cp);
            //dd($page_cp);

            $this->logger->info($this->translator->trans("Page No ". $page_cp->getNumeroPagecp() . " " . " from document CP No ". $event->getDocumentCp()->getNumeroDoccp(). " has been created by ". $event->getDocumentCp()->getCreatedBy()));
        }
        $this->doctrine->getManager()->flush();
    }


}