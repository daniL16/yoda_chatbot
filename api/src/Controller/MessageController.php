<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ProcessMessageService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class MessageController
{

    public function __construct(private ProcessMessageService $messageService)
    {
    }

    public function send(Request $request): JsonResponse
    {
        $errors = [];
        if(!$this->validateRequest($request, $errors)){
            return new JsonResponse(['errors' => $errors], 400);
        }

        $data = json_decode((string)$request->getContent(), true);

        try {
            $failedAttempts = $data['notFoundAttempts'] ?? 0;
            return new JsonResponse(
                $this->messageService->getResponseMessage($data['message'],
                $data['sessionToken'] ?? '',
                (int)$failedAttempts),
                200);
        } catch (GuzzleException $exception) {
            return new JsonResponse(['errors' => $exception->getMessage()],500);
        }
    }

    /**
     * @param Request $request
     * @param array<String> $errors
     * @return bool
     */
    private function validateRequest(Request $request, array &$errors) : bool{
         $data = json_decode((string) $request->getContent(), true);
         $valid = true;
        if (JSON_ERROR_NONE !== json_last_error()) {
            $errors[] =  sprintf('Invalid JSON format: %s', json_last_error_msg());
            $valid = false;
        }
        if (empty($data['message'])) {
            $errors[] = 'Message is required';
            $valid = false;
        }
        return $valid;
    }


}
