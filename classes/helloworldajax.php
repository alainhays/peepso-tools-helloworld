<?php

class PeepSoHelloworldAjax extends PeepSoAjaxCallback
{
	public function hello(PeepSoAjaxResponse $resp)
	{
		$hello = $this->_input->_i('hello', 'hello');

		$resp->success(TRUE);
		$resp->set('hello', $hello);
	}
}