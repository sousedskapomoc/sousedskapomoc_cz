<?php declare(strict_types = 1);

namespace SousedskaPomoc\Api\V1;

use Apitte\Core\Mapping\Request\BasicEntity;
use Symfony\Component\Validator\Constraints as Assert;


final class VoiceCallRequest extends BasicEntity
{

	/**
	 * @var string
	 * TODO NotNull?
	 * @Assert\Type("string")
	 */
	public $phoneNumer;

	/**
	 * @var string
	 * TODO NotNull?
	 * @Assert\Type(type="string")
	 */
	public $sourceFile;

	/**
	 * @var string
	 * TODO NotNull?
	 * @Assert\Type(type="string")
	 */
	public $stt_content;

	/**
	 * TODO
	 */
	public $processed;

	/**
	 * TODO
	 */
	public $posted_order;

	/**
	 * @var string
	 * TODO NotNull?
	 * @Assert\DateTime
	 */
	public $createdAt;

	/**
	 * @var string
	 * TODO NotNull?
	 * @Assert\DateTime
	 */
	public $updatedAt;

}
