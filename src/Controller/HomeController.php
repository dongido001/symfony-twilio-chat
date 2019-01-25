<?php
    
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;

class HomeController extends AbstractController
{
     /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/token", name="get_token", methods={"POST"})
     */
    public function authenticate( Request $request ) {

        $identity = $request->request->get( 'email' );

        // Required for all Twilio access tokens
        $twilioAccountSid = getenv(  'ACCOUNT_SID' );
        $twilioApiKey     = getenv(  'API_KEY' );
        $twilioApiSecret  = getenv(  'API_SECRET' );

        // Required for Chat grant
        $serviceSid = getenv( 'SERVICE_INSTANCE_SID' );

        // Create access token, which we will serialize and send to the client
        $token = new AccessToken(
            $twilioAccountSid,
            $twilioApiKey,
            $twilioApiSecret,
            3600,
            $identity
        );

        // Create Chat grant
        $chatGrant = new ChatGrant();
        $chatGrant->setServiceSid( $serviceSid);

        // Add grant to token
        $token->addGrant( $chatGrant );

        // render token to json
        return $this->json([
            "status" => "success",
            "token" => $token->toJWT()
        ]);
    }

}