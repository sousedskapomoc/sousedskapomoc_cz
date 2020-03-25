<?php declare(strict_types = 1);

namespace SousedskaPomoc\Api\V1;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestMapper;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;


/**
 * @ControllerPath("/calls")
 */
class VoiceCallController extends BaseV1Controller
{

	/**
	 * @Path("/")
	 * @Method("GET")
	 */
	public function get(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response->writeJsonBody([
			[
				'id' => 1,
				'phoneNumber' => '123456789',
				'sourceFile' => null,
				'stt_content' => 'Lorem ipsum dolor sit amet...',
				'processed' => '?',
				'posted_order' => '?',
				'createdAt' => new \DateTimeImmutable(),
				'updatedAt' => new \DateTimeImmutable(),
			]
		]);
	}


	/**
	 * @Path("/")
	 * @Method("POST")
	 * @RequestMapper(entity="SousedskaPomoc\Api\V1\VoiceCallRequest")
	 */
	public function post(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$entity = $request->getEntity();
		assert($entity instanceof VoiceCallRequest);
		// TODO store to db
		return $response->writeJsonBody($request->getJsonBody());
	}

}
