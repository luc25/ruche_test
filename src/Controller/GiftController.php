<?php

namespace App\Controller;

use App\Form\GiftsFileType;
use App\Service\GiftService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/api/gift")
 */
class GiftController extends AbstractController
{
    /**
     * @Route("/upload", methods={"POST"}, name="gift_upload")
     */
    public function upload(Request $request, GiftService $giftService): Response
    {
        $form = $this->createForm(GiftsFileType::class);
        $data = $request->request->all();
        $data['file'] = $request->files->get('file');
        $form->submit($data);

        if ($form->isValid()) {
            $result = $giftService->createGiftsFromFile($form['warehouse']->getData(), $form['file']->getData());

            if (!$result) {
                return new Response(json_encode(['data' => $giftService->getStatistics($form['warehouse']->getData())]));
            }
        }

        return new Response(json_encode(['error' => 'File must be a csv']));
    }

    /**
     * @Route("/statistics", methods={"GET"}, name="gift_statistics")
     */
    public function statistics(Request $request, GiftService $giftService): Response
    {
        return new Response(json_encode(['data' => $giftService->getStatistics($request->query->get('warehouse'))]));
    }
}
