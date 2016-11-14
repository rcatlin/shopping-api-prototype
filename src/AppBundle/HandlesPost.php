<?php

namespace AppBundle;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

trait HandlesPost
{
    /**
     * @param Form $form
     * @param Request $request
     *
     * @return Form
     */
    public function handlePost(Form $form, Request $request) {
        return $form
            ->handleRequest($request)
            ->submit(
                json_decode($request->getContent(), true),
                'PATCH' !== $request->getMethod()
            );
    }
}
