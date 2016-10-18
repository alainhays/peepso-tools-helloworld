<?php

class PeepSoHelloworldAjax extends PeepSoAjaxCallback
{
	public function hello(PeepSoAjaxResponse $resp)
	{
		$hello = $this->_input->get_int('hello', 'hello');

		$resp->success(TRUE);
		$resp->set('hello', $hello);
	}
}