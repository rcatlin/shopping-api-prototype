<?php

namespace AppBundle;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;

trait RenderFormErrors
{
    public function renderFormErrors(Form $form, array $headers = [])
    {
        if ($form->isValid()) {
            return null;
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        $headers['Content-Type'] = 'text/json';

        return new Response(
            json_encode($errors),
            400,
            $headers
        );
    }
}
