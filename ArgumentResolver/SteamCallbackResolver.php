<?php

namespace Knojector\SteamAuthenticationBundle\ArgumentResolver;

use Knojector\SteamAuthenticationBundle\DTO\SteamCallback;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author knojector <dev@knojector.xyz>
 */
class SteamCallbackResolver implements ValueResolverInterface
{
    public function __construct(private ValidatorInterface $validator)
    {}

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== SteamCallback::class) {
            return [];
        }

        $steamCallback = SteamCallback::fromRequest($request);

        $errors = $this->validator->validate($steamCallback);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        yield $steamCallback;
    }
}