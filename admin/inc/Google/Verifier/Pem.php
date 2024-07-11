<?php
/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
 
require_once ADSFORWP_LIB_PATH . 'Google/Auth/Exception.php';
require_once ADSFORWP_LIB_PATH . 'Google/Verifier/Abstract.php';

/**
 * Verifies signatures using PEM encoded certificates.
 *
 * @author Brian Eaton <beaton@google.com>
 */
class Adsforwp_Google_Verifier_Pem extends Adsforwp_Google_Verifier_Abstract
{
  private $publicKey;

  /**
   * Constructs a verifier from the supplied PEM-encoded certificate.
   *
   * $pem: a PEM encoded certificate (not a file).
   * @param $pem
   * @throws Adsforwp_Google_Auth_Exception
   * @throws Adsforwp_Google_Exception
   */
  public function __construct($pem)
  {
    if (!function_exists('openssl_x509_read')) {
      throw new Adsforwp_Google_Exception('Google API PHP client needs the openssl PHP extension');
    }
    $this->publicKey = openssl_x509_read($pem);
    if (!$this->publicKey) {
      throw new Adsforwp_Google_Auth_Exception("Unable to parse PEM: ".esc_html($pem));
    }
  }

  public function __destruct()
  {
    if ($this->publicKey) {
      openssl_x509_free($this->publicKey);
    }
  }

  /**
   * Verifies the signature on data.
   *
   * Returns true if the signature is valid, false otherwise.
   * @param $data
   * @param $signature
   * @throws Adsforwp_Google_Auth_Exception
   * @return bool
   */
  public function verify($data, $signature)
  {
    $hash = defined("OPENSSL_ALGO_SHA256") ? OPENSSL_ALGO_SHA256 : "sha256";
    $status = openssl_verify($data, $signature, $this->publicKey, $hash);
    if ($status === -1) {
      throw new Adsforwp_Google_Auth_Exception('Signature verification error: ' . esc_html(openssl_error_string()));
    }
    return $status === 1;
  }
}
