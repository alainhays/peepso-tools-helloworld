<?php

class PeepSoConfigSectionHello extends PeepSoConfigSectionAbstract
{
	public static $css_overrides = array(
		'appearance-avatars-square',
	);

	// Builds the groups array
	public function register_config_groups()
	{
		$this->context='left';
		$this->group_hello();

		$this->context='right';
		$this->group_world();
	}

	private function group_hello()
	{
		// # Use Custom Greeting
		$this->set_field(
			'peepso_helloworld_use_custom',
			__('Use Custom Greeting', 'peepsohelloworld'),
			'yesno_switch'
		);

		// # Message Custom Greeting
		$this->set_field(
			'peepso_helloworld_use_custom_message',
			__('Switch this on to enable the custom greeting in the frontend','peepsohelloworld'),
			'message'
		);

		$this->set_group(
			'peepso_helloworld_hello',
			__('Hello', 'peepsohelloworld')
		);
	}

	private function group_world()
	{

		// # Message Custom Greeting
		$this->set_field(
			'peepso_helloworld_description',
			__('Put a custom greeting message here', 'peepsohelloworld'),
			'message'
		);

		$this->set_field(
			'peepso_helloworld_custom_message',
			__('Custom Greeting', 'peepsohelloworld'),
			'text'
		);

		$this->set_group(
			'peepso_helloworld_world',
			__('World', 'peepsohelloworld')
		);
	}
}