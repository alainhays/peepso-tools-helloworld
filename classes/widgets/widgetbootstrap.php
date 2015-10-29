<?php


class PeepSoBOOTSTRAPWidgetBOOTSTRAP extends WP_Widget
{

	public function __construct( $id = NULL, $name = NULL, $args= NULL ) {

		$id     = ( NULL !== $id )  ? $id   : 'PeepSoBOOTSTRAPWidgetBOOTSTRAP';
		$name   = ( NULL !== $name )? $name : __('PeepSo BOOTSTRAP', 'peepsoBOOTSTRAP');
		$args   = ( NULL !== $args )? $args : array('description' => __('BOOTSTRAP', 'peepsoBOOTSTRAP'),);

		parent::__construct(
			$id,
			$name,
			$args
		);
	}


	public function widget( $args, $instance ) {

		$view_id = NULL;

		if (isset($instance['is_profile_widget'])) {
			// Use currently viewed profile
			$view_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();

			// Override the HTML wrappers
			$args = apply_filters('peepso_widget_args_internal', $args);
		}

		// Additional shared adjustments
		$instance = apply_filters('peepso_widget_instance', $instance);

		if (!$view_id) {
			$view_id = get_current_user_id();
		}

		// actually friends code, for the record

		$instance['template'] = 'friends.tpl';


		if (!array_key_exists('user_id', $instance)) {
			$instance['user_id'] = $view_id;
		}

		if (!array_key_exists('search_args', $instance)) {
			$instance['search_args'] = array(
				'number' => $instance['limit'],
			);
		}

		// @TODO guest should be able to see the friends of another person if privacy allows it
		if (!$instance['user_id']) {
			return false;
		}

		if (!array_key_exists('list', $instance)) {
			$friendsModel = PeepSoFriendsModel::get_instance();
			$instance['list'] = $friendsModel->get_friends($instance['user_id'], $instance['search_args']);
		}

		PeepSoTemplate::exec_template( 'widgets', $instance['template'], array( 'args'=>$args, 'instance' => $instance ) );
	}

	/**
	 * Outputs the admin options form
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$instance['fields'] = array(
			// general
			'limit'     => TRUE,
			'title'     => TRUE,

			// peepso
			'integrated'   => TRUE,
			'position'  => TRUE,
			'hideempty' => TRUE,
		);

		$this->instance = $instance;

		$settings =  apply_filters('peepso_widget_form', array('html'=>'', 'that'=>$this,'instance'=>$instance));
		echo $settings['html'];
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['limit']       = (int) $new_instance['limit'];

		$instance['hideempty']   = (int) $new_instance['hideempty'];
		$instance['position']    = strip_tags($new_instance['position']);

		return $instance;
	}
}

// EOF