<?php do_action( 'kanban_board_template_before', $wp_query->query_vars['kanban']->boards ); ?>



<?php include Kanban_Template::find_template( 'board/header' ); ?>

<?php echo apply_filters( 'kanban_page_boards_before', '' ); ?>

<div class="tab-content">
<div id="page-loading">
	<span class="hidden-xs glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>
</div>
<?php foreach ( $wp_query->query_vars['kanban']->boards as $board_id => $board ) : ?>
<?php include Kanban_Template::find_template( 'board/board' ); ?>
<?php endforeach ?>
</div><!-- tab-content -->

<?php echo apply_filters( 'kanban_page_boards_after', '' ); ?>

<?php include Kanban_Template::find_template( 'board/modal-projects' ); ?>
<?php include Kanban_Template::find_template( 'board/modal-keyboard-shortcuts' ); ?>


<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

var alert = "<?php echo addslashes($wp_query->query_vars['kanban']->alert); ?>";
var text = <?php echo json_encode( apply_filters( 'kanban_board_text', $wp_query->query_vars['kanban']->text) ); ?>;

var templates = {};
var window_w, window_h, screen_size, scrollbar_w;
var is_dragging = false;
var current_user = {
	has_cap: function()
	{
		return false;
	}
};

var url_params = {
	board_id: <?php echo $wp_query->query_vars['kanban']->current_board_id ?>
};

<?php if ( !empty($_GET) ) : foreach ($_GET as $key => $value) : ?>
<?php if ( is_array($value) ) : ?>
url_params['<?php echo str_replace(array('\'', '"'), '', $key) ?>'] = <?php echo json_encode( $value ) ?>;
<?php else : ?>
url_params['<?php echo str_replace(array('\'', '"'), '', $key) ?>'] = '<?php echo $value ?>';
<?php endif ?>
<?php endforeach; endif ?>

var boards = [];
<?php foreach ( $wp_query->query_vars['kanban']->boards as $board_id => $board ) : ?>
boards[<?php echo $board_id ?>] = {
	id: function ()
	{
		return <?php echo $board_id ?>;
	},
	title: function ()
	{
		return '<?php echo $board->title ?>';
	},
	col_percent_w: function ()
	{
		return <?php echo $board->col_percent_w ?>;
	},
	status_w: function ()
	{
		return <?php echo $board->status_w ?>;
	},
	settings: function ()
	{
		return <?php echo json_encode( $board->settings, JSON_UNESCAPED_UNICODE ); ?>;
	},
	filters: <?php echo json_encode( $board->filters, JSON_UNESCAPED_UNICODE ); ?>,
	search: <?php echo json_encode( $board->search, JSON_UNESCAPED_UNICODE ); ?>,
	status_records: function ()
	{
		return <?php echo (json_encode( $board->statuses, JSON_UNESCAPED_UNICODE )); ?>;
	},
	tasks: <?php echo Kanban_Utils::slashes(json_encode( $board->tasks, JSON_UNESCAPED_UNICODE )); ?>,
	project_records: <?php echo Kanban_Utils::slashes(json_encode( $board->projects, JSON_UNESCAPED_UNICODE )); ?>,
	allowed_users: function ()
	{
		return <?php echo Kanban_Utils::slashes(json_encode( $board->allowed_users, JSON_UNESCAPED_UNICODE )); ?>;
	},
	current_user_id: function ()
	{
		return <?php echo $wp_query->query_vars['kanban']->current_user_id ?>;
	},
	estimate_records: function ()
	{
		return <?php echo json_encode( $board->estimates, JSON_UNESCAPED_UNICODE ); ?>;
	}
	<?php echo apply_filters( 'kanban_board_js_onpage', '' ); ?>
};
<?php endforeach // boards ?>

var current_board_id = <?php echo $wp_query->query_vars['kanban']->current_board_id ?>;

</script>




<style>
<?php foreach ( $wp_query->query_vars['kanban']->boards as $board_id => $board ) : ?>
#board-<?php echo $board_id ?> .col_percent_w {width: <?php echo $board->col_percent_w ?>%}
#board-<?php echo $board_id ?> .status_w {width: <?php echo $board->status_w ?>%}

#board-<?php echo $board_id ?> .row-tasks,
#board-<?php echo $board_id ?> .row-statuses {
	margin-left: -<?php echo $board->status_w ?>%;
	width: <?php echo 100+($board->status_w*2) ?>%;
}
<?php endforeach // boards ?>
</style>



<?php foreach ( $wp_query->query_vars['kanban']->boards as $board_id => $board ) : ?>
<?php do_action( 'kanban_board_render_js_templates', $board ); ?>
<?php endforeach // boards ?>



<?php include Kanban_Template::find_template( 'board/footer' ); ?>