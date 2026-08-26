<?php

/* 
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *  This API has now modified by VeoZax under GNU Lesser General Public License.
 *  Feel free to use it + if you are willing to modify or Enhance this API,
 *  Make sure to publish your changes to the GitHub open sourced.
 *  Do Not Own This API Privately Since this API is made to use Freely for Every
 *  Legacy users from 0.14.x - 0.15.10 - 1.1.x
 *   
 *               ╦  ╦┌─┐┌─┐╔═╗┌─┐─┐ ┬  ╔═╗┌─┐┬
 *               ╚╗╔╝├┤ │ │╔═╝├─┤┌┴┬┘  ╠═╣├─┘│
 *                ╚╝ └─┘└─┘╚═╝┴ ┴┴ └─  ╩ ╩┴  ┴
 *  
 *  	         » Multi-Version API by VeoZax 
 *             » Accepted MCPE Versions: 0.14x - 0.15.10 - 1.1.x
 *  			     » YouTube: @VeoZax
 *            » Discord: https://discord.gg/dCzgPYam2J
 *               » Website: https://info.veozax.xyz
 */


declare(strict_types=1);
namespace pocketmine\network\mcpe\protocol;
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\EducationSettingsAgentCapabilities;use pocketmine\network\mcpe\protocol\types\EducationSettingsExternalLinkSettings;
class EducationSettingsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::EDUCATION_SETTINGS_PACKET;
	private $codeBuilderDefaultUri;
	private $codeBuilderTitle;
	private $canResizeCodeBuilder;
	private $disableLegacyTitleBar;
	private $postProcessFilter;
	private $screenshotBorderResourcePath;
	private $agentCapabilities;
	private $codeBuilderOverrideUri;
	private $hasQuiz;
	private $linkSettings;
	public static function create(
		string $codeBuilderDefaultUri,
		string $codeBuilderTitle,
		bool $canResizeCodeBuilder,
		bool $disableLegacyTitleBar,
		string $postProcessFilter,
		string $screenshotBorderResourcePath,
		?EducationSettingsAgentCapabilities $agentCapabilities,
		?string $codeBuilderOverrideUri,
		bool $hasQuiz,
		?EducationSettingsExternalLinkSettings $linkSettings
	) : self{
		$result = new self;
		$result->codeBuilderDefaultUri = $codeBuilderDefaultUri;
		$result->codeBuilderTitle = $codeBuilderTitle;
		$result->canResizeCodeBuilder = $canResizeCodeBuilder;
		$result->disableLegacyTitleBar = $disableLegacyTitleBar;
		$result->postProcessFilter = $postProcessFilter;
		$result->screenshotBorderResourcePath = $screenshotBorderResourcePath;
		$result->agentCapabilities = $agentCapabilities;
		$result->codeBuilderOverrideUri = $codeBuilderOverrideUri;
		$result->hasQuiz = $hasQuiz;
		$result->linkSettings = $linkSettings;
		return $result;
	}
	public function getCodeBuilderDefaultUri() : string{
		return $this->codeBuilderDefaultUri;
	}
	public function getCodeBuilderTitle() : string{
		return $this->codeBuilderTitle;
	}
	public function canResizeCodeBuilder() : bool{
		return $this->canResizeCodeBuilder;
	}
	public function disableLegacyTitleBar() : bool{ return $this->disableLegacyTitleBar; }
	public function getPostProcessFilter() : string{ return $this->postProcessFilter; }
	public function getScreenshotBorderResourcePath() : string{ return $this->screenshotBorderResourcePath; }
	public function getAgentCapabilities() : ?EducationSettingsAgentCapabilities{ return $this->agentCapabilities; }
	public function getCodeBuilderOverrideUri() : ?string{
		return $this->codeBuilderOverrideUri;
	}
	public function getHasQuiz() : bool{
		return $this->hasQuiz;
	}
	public function getLinkSettings() : ?EducationSettingsExternalLinkSettings{ return $this->linkSettings; }
	protected function decodePayload() : void{
		$this->codeBuilderDefaultUri = $this->getString();
		
		
	}
	protected function encodePayload() : void{
		$this->putString($this->codeBuilderDefaultUri);
		
		
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleEducationSettings($this);
	}}