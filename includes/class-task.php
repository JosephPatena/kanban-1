<?php



// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



Kanban_Task::init();


class Kanban_Task extends Kanban_Db
{
	private static $instance;
	static $slug = 'task';
	protected static $table_name = 'tasks';
	protected static $table_columns = array(
		'title' => 'text',
		'description' => 'text',
		'created_dt_gmt' => 'datetime',
		'modified_dt_gmt' => 'datetime',
		'user_id_author' => 'int',
		'user_id_assigned' => 'int',
		'status_id' => 'int',
		'project_id' => 'int',
		'estimate_id' => 'int'
	);



	static function init()
	{
		// add_action( 'wp', array(__CLASS__, 'post_save') );
		add_action( sprintf('wp_ajax_save_%s', self::$slug), array(__CLASS__, 'ajax_save') );
		add_action( sprintf('wp_ajax_delete_%s', self::$slug), array(__CLASS__, 'ajax_delete') );

		add_action( 'add_meta_boxes', array(__CLASS__, 'add_comments_meta_box') );

	}



	static function format_hours($hours)
	{
		if ( $hours < 0 )
		{
			$hours = 0;
		}

		if ( $hours < 1 )
		{
			$label = sprintf('%sm', ceil($hours*60/100));
		}
		elseif ( $hours < 8 )
		{
			$label = sprintf('%sh', $hours);
		}
		else
		{
			$label = sprintf('%sd %sh', floor($hours/8), $hours % 8);
		}

		return $label;
	}



	static function ajax_save ()
	{
		if (  !isset( $_POST[Kanban_Utils::get_nonce()] ) || ! wp_verify_nonce( $_POST[Kanban_Utils::get_nonce()], sprintf('%s-save', Kanban::get_instance()->settings->basename)) || !is_user_logged_in() ) wp_send_json_error();



		do_action( sprintf('%s_before_%s_ajax_save', Kanban::get_instance()->settings->basename, self::$slug) );



		$_POST['task']['modified_dt_gmt'] = gmdate('Y-m-d H:i:s');
		$_POST['task']['user_id_author'] = get_current_user_id();



		if ( isset($_POST['task']['id']) )
		{
			self::update($_POST['task'], array('id' => $_POST['task']['id']));
			$task_id = $_POST['task']['id'];
		}
		else
		{
			$_POST['task']['created_dt_gmt'] = gmdate('Y-m-d H:i:s');
			$task_id = self::insert($_POST['task']);
		}



		$post_data = self::get_row('id', $task_id);

		if ( !$post_data ) wp_send_json_error();



		Kanban_Status_Change::add($task_id, $post_data->status_id);



		if ( !empty($_POST['comment']) )
		{
			Kanban_Comment::add(
				$_POST['comment'],
				'system',
				$task_id
			);
		}



		if ( isset($_POST['status_id_old']) )
		{
			Kanban_Status_Change::add(
				$task_id,
				$_POST['status_id'],
				$_POST['status_id_old']
			);
		}



		do_action( sprintf('%s_after_%s_ajax_save', Kanban::get_instance()->settings->basename, self::$slug) );



		wp_send_json_success(array(
			'message' => sprintf('%s saved', self::$slug),
			self::$slug => $post_data
		));
	}



	static function ajax_delete ()
	{
		if (  !isset( $_POST[Kanban_Utils::get_nonce()] ) || ! wp_verify_nonce( $_POST[Kanban_Utils::get_nonce()], sprintf('%s-save', Kanban::get_instance()->settings->basename)) || $_POST['post_type'] !== Kanban_Post_Types::format_post_type(self::$slug) || !is_user_logged_in() ) wp_send_json_error();



		do_action( sprintf('%s_before_%s_ajax_delete', Kanban::get_instance()->settings->basename, self::$slug) );



		$is_successful = Kanban_Post::delete($_POST);



		do_action( sprintf('%s_after_%s_ajax_delete', Kanban::get_instance()->settings->basename, self::$slug) );



		if ( $is_successful )
		{
			wp_send_json_success(array(
				'message' => sprintf('%s deleted', self::$slug)
			));
		}
		else
		{
			wp_send_json_error(array(
				'message' => sprintf('Error deleting %s', self::$slug)
			));
		}
	}



	static function get_all ($sql = NULL)
	{
		$table_name = self::table_name();

		$worked_table_name = Kanban_Task_Hour::table_name();




		$sql = "SELECT tasks.*,
				COALESCE(SUM(worked.hours), 0) 'hour_count'
				FROM {$table_name} tasks
				LEFT JOIN {$worked_table_name} worked
				ON tasks.id = worked.task_id
				WHERE tasks.is_active = 1
				GROUP BY tasks.id
		;";

		$sql = apply_filters(
			sprintf('%s_sql_%s_get_all', Kanban::get_instance()->settings->basename, self::$slug),
			$sql
		);

		$records = parent::get_all($sql);

		return Kanban_Utils::build_array_with_id_keys ($records, 'id');;
	}



	static function db_table ()
	{
		return "CREATE TABLE " . self::table_name() . " (
					id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					title text NOT NULL,
					description text NOT NULL,
					created_dt_gmt datetime NOT NULL,
					modified_dt_gmt datetime NOT NULL,
					user_id_author bigint(20) NOT NULL,
					user_id_assigned bigint(20) NOT NULL,
					status_id bigint(20) NOT NULL,
					project_id bigint(20) NOT NULL,
					estimate_id bigint(20) NOT NULL,
					is_active BOOLEAN NOT NULL DEFAULT TRUE,
					PRIMARY KEY  (id),
					KEY is_active (is_active)
				)";
	} // db_table



	public static function get_instance()
	{
		if ( ! self::$instance )
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

} // Kanban_Task



