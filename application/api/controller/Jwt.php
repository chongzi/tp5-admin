<?php

namespace app\api\controller;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;

trait Jwt {
	protected $isJwt = true;

	// 无认证状态也可访问的接口
	protected $allowActions = [
		'reg',
		'code',
		'login',
		'resetPassword',
		'snsLogin',
		'wxappLogin',
		'cityList',
		'topList',
		'getAppInfo',
		'getGiftList'

	];

	// 有效期，单位秒
	private $expiration = 0;
	private $token;

	/**
	 * 获取JWT请求数据
	 *
	 *     验证成功后会注入请求用户
	 *
	 * @Author   slpi1
	 * @Email    365625906@gmail.com
	 * @DateTime 2018-03-02T11:50:46+0800
	 * @return   void
	 */
	protected function jwtData() {
		$this->log('jwt');

		/*dump($this->authenticate());
		exit;*/
		// 免验接口定义
		if ($this->allowCheck()) {

			$this->data = $this->request->param();
			// 免验接口尝试注入用户数据
			if ($user_id = $this->authenticate()) {
				// 注入认证用户
				$this->data['user_id'] = $user_id;
			}
		} 
        else if ($user_id = $this->authenticate()) {
			$this->data = $this->request->param();
			// 注入认证用户
			$this->data['user_id'] = $user_id;

		} 
        else {
			$this->apkReturn('登录信息已过期，请重新登录', 401);
		}
	}

	/**
	 * 按登录用户创建token
	 * @Author   slpi1
	 * @Email    365625906@gmail.com
	 * @DateTime 2018-03-02T11:51:41+0800
	 * @param    int                   $user_id 登录用户ID
	 * @return   array
	 */
	protected function getToken($user_id) {

		$domain = config('api_domain');
		require_once '../Vendor/lcobucci/jwt/src/Builder.php';
		require_once '../Vendor/lcobucci/jwt/src/Signature.php';
		require_once '../Vendor/lcobucci/jwt/src/Token.php';
		require_once '../Vendor/lcobucci/jwt/src/Parsing/Encoder.php';
		require_once '../Vendor/lcobucci/jwt/src/Claim.php';
		require_once '../Vendor/lcobucci/jwt/src/Claim/Factory.php';
		require_once '../Vendor/lcobucci/jwt/src/Claim/Basic.php';
		require_once '../Vendor/lcobucci/jwt/src/Claim/Validatable.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer/Key.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer/Keychain.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer/BaseSigner.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer/Rsa.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer/Rsa/Sha256.php';
		require_once '../Vendor/lcobucci/jwt/src/Claim/EqualsTo.php';
		$builder = new Builder();
		$builder->setIssuer($domain);
		$builder->set('uid', $user_id);

		// 设置过期时间
		if ($this->expiration > 0) {
			$builder->setExpiration(time() + $this->expiration);
		}

		// 签名
		$signer = new Sha256();
		$keychain = new Keychain();
		$builder->sign($signer, $keychain->getPrivateKey('file://' . $this->private_key_file));

		// 获取token
		$this->token = $builder->getToken();
		$this->token->getHeaders();
		$this->token->getClaims();
		$token = (string) $this->token;
		return [
			'token' => $token,
		];
	}

	/**
	 * token 验证
	 * @Author   slpi1
	 * @Email    365625906@gmail.com
	 * @DateTime 2018-03-02T11:52:24+0800
	 * @return   Boolean|int
	 */
	protected function authenticate() {

		$token = $this->request->header('Authorization');

		if (!isset($token)) {
			return false;
		} else {

			list($token_type, $tokenContent) = split(" +", $token);

			$this->parse($tokenContent);
		}
		if ($this->validation()) {
			return $this->token->getClaim('uid');
		} else {
			return false;
		}
	}

	/**
	 * 解析token
	 * @Author   slpi1
	 * @Email    365625906@gmail.com
	 * @DateTime 2018-03-02T11:52:47+0800
	 * @param    string                   $token token字符串
	 * @return   void
	 */
	protected function parse($token) {
		require_once '../Vendor/lcobucci/jwt/src/Parser.php';
		require_once '../Vendor/lcobucci/jwt/src/ValidationData.php';
		require_once '../Vendor/lcobucci/jwt/src/Signature.php';
		require_once '../Vendor/lcobucci/jwt/src/Token.php';
		require_once '../Vendor/lcobucci/jwt/src/Parsing/Decoder.php';
		require_once '../Vendor/lcobucci/jwt/src/Claim.php';
		require_once '../Vendor/lcobucci/jwt/src/Claim/Factory.php';
		require_once '../Vendor/lcobucci/jwt/src/Claim/Basic.php';
		require_once '../Vendor/lcobucci/jwt/src/Claim/Validatable.php';
		require_once '../Vendor/lcobucci/jwt/src/Claim/EqualsTo.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer/Key.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer/Keychain.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer/BaseSigner.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer/Rsa.php';
		require_once '../Vendor/lcobucci/jwt/src/Signer/Rsa/Sha256.php';
		$this->token = (new Parser())->parse((string) $token);
		$this->token->getHeaders();
		$this->token->getClaims();

	}

	/**
	 * token 属性及过期时间等验证
	 * @Author   slpi1
	 * @Email    365625906@gmail.com
	 * @DateTime 2018-03-02T11:53:01+0800
	 * @return   boolean
	 */
	protected function validation() {
		$domain = config('api_domain');
		$data = new ValidationData();
		$data->setIssuer($domain);
		if (!$this->token->validate($data)) {
			return false;
		}

		// 验签
		$signer = new Sha256();
		$keychain = new Keychain();
		if (!$this->token->verify($signer, $keychain->getPublicKey('file://' . $this->public_key_file))) {
			return false;
		}
		return true;
	}

	/**
	 * 接口免验规则
	 * @Author   slpi1
	 * @Email    365625906@gmail.com
	 * @DateTime 2018-03-07T10:07:37+0800
	 * @return   boolean
	 */
	protected function allowCheck() {

		$dispatch = $this->request->dispatch();
		$action = $dispatch['module']['2'];
		// 用户部分免验接口
		if (in_array($action, $this->allowActions)) {
			return true;
		}

		// 其他接口自定义免验规则
		if (!empty($this->publicActions) && in_array($action, $this->publicActions)) {
			return true;
		}
		return false;
	}
}
