<?php
namespace Conpherence\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AMFController extends Controller
{
    protected function createAMFResponse($object, $status = \Symfony\Component\HttpFoundation\Response::HTTP_OK)
    {
        return Response::create(amf_encode($object, AMF_CLASS_MAPPING), $status);
    }

    protected function readAMFRequest(Request $request)
    {
        return amf_decode($request->getContent());
    }
} 