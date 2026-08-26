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

 return array(
    'root' => array(
        'name' => 'pocketmine/pocketmine-mp',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => null,
        'type' => 'project',
        'install_path' => __DIR__ . '/../../../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        'adhocore/json-comment' => array(
            'pretty_version' => 'v0.0.7',
            'version' => '0.0.7.0',
            'reference' => '135356c7e7336ef59924f1d921c770045f937a76',
            'type' => 'library',
            'install_path' => __DIR__ . '/../adhocore/json-comment',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'daverandom/callback-validator' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => 'd87a08cddbc6099816ed01e50ce25cdfc43b542f',
            'type' => 'library',
            'install_path' => __DIR__ . '/../daverandom/callback-validator',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
        'fgrosse/phpasn1' => array(
            'pretty_version' => 'v2.5.0',
            'version' => '2.5.0.0',
            'reference' => '42060ed45344789fb9f21f9f1864fc47b9e3507b',
            'type' => 'library',
            'install_path' => __DIR__ . '/../fgrosse/phpasn1',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'multimcpe/binaryutils' => array(
            'pretty_version' => '0.1.9',
            'version' => '0.1.9.0',
            'reference' => '564f2b6b56bed4d955b6a436d9c81ffc28943929',
            'type' => 'library',
            'install_path' => __DIR__ . '/../multimcpe/binaryutils',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'multimcpe/math' => array(
            'pretty_version' => '0.2.0',
            'version' => '0.2.0.0',
            'reference' => '7e32f5f5a958e89f7667ea603179a230119b45f1',
            'type' => 'library',
            'install_path' => __DIR__ . '/../multimcpe/math',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'multimcpe/nbt' => array(
            'pretty_version' => '0.2.10',
            'version' => '0.2.10.0',
            'reference' => 'b6a74519248869079a59eb0839f62694b29789e9',
            'type' => 'library',
            'install_path' => __DIR__ . '/../multimcpe/nbt',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'multimcpe/raklib' => array(
            'pretty_version' => '0.12.5',
            'version' => '0.12.5.0',
            'reference' => '1509eeea9e1e4a639bab5ed68db69a9a2fae7136',
            'type' => 'library',
            'install_path' => __DIR__ . '/../multimcpe/raklib',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'multimcpe/snooze' => array(
            'pretty_version' => '0.1.0',
            'version' => '0.1.0.0',
            'reference' => '59faca98ad385e749b5325649e750ceb3b58c3e7',
            'type' => 'library',
            'install_path' => __DIR__ . '/../multimcpe/snooze',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'multimcpe/spl' => array(
            'pretty_version' => '0.3.0',
            'version' => '0.3.0.0',
            'reference' => '7172942dad788ae8d68aaafa013197d43589e666',
            'type' => 'library',
            'install_path' => __DIR__ . '/../multimcpe/spl',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'pocketmine/errorhandler' => array(
            'pretty_version' => '0.7.0',
            'version' => '0.7.0.0',
            'reference' => 'cae94884368a74ece5294b9ff7fef18732dcd921',
            'type' => 'library',
            'install_path' => __DIR__ . '/../pocketmine/errorhandler',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'pocketmine/pocketmine-mp' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => null,
            'type' => 'project',
            'install_path' => __DIR__ . '/../../../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),);