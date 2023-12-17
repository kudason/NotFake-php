<?php include 'header_browse.php';?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/frontend/' . $selected_theme;?>/hovercss/demo.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/frontend/' . $selected_theme;?>/hovercss/set1.css" />
<style>
	.movie_thumb{}
	.btn_opaque{font-size:20px; border: 1px solid #939393;text-decoration: none;margin: 10px;background-color: rgba(0, 0, 0, 0.74); color: #fff;}
	.btn_opaque:hover{border: 1px solid #939393;text-decoration: none;background-color: rgba(57, 57, 57, 0.74);color:#fff;}
</style>
<!-- PHẦN NỔI BẬT TRÊM ĐẦU -->
<?php
	$featured_movie		=	$this->db->get_where('movie', array('featured'=>1))->row();
	
	?>
<div style="background-image: url(<?php echo $this->crud_model->get_poster_url('movie' , $featured_movie->movie_id);?>); background-size:cover; width: 100%;">
	<div class="mobile_responsive" style="font-weight: bold;color: #fff;">
		<h1 class="mobile_responsive_text_bold"><?php echo $featured_movie->title;?></h1>
		<h5 class="mobile_responsive_sort_description" style="color: #ccc; max-width: 900px;"><?php echo $featured_movie->description_short;?></h5>
		<a href="<?php echo base_url();?>index.php?browse/playmovie/<?php echo $featured_movie->movie_id;?>" 
			class="btn btn-danger btn-lg" style="font-size: 20px;"> 
		<b><i class="fa fa-play"></i> <?php echo get_phrase('PLAY');?></b>
		</a>
		<!-- THÊM HOẶC XÓA KHỎI DANH SÁCH PHÁT -->
		<span id="mylist_button_holder">
		</span>
		<span id="mylist_add_button" style="display:none;">
		<a href="#" class="btn  btn-lg btn_opaque"
			onclick="process_list('movie' , 'add', <?php echo $featured_movie->movie_id;?>)"> 
		<b><i class="fa fa-plus"></i> <?php echo get_phrase('MY_LIST');?></b>
		</a>
		</span>
		<span id="mylist_delete_button" style="display:none;">
		<a href="#" class="btn  btn-lg btn_opaque"
			onclick="process_list('movie' , 'delete', <?php echo $featured_movie->movie_id;?>)"> 
		<b><i class="fa fa-check"></i> <?php echo get_phrase('MY_LIST');?></b>
		</a>
		</span>
	</div>
</div>
<script>
	// gửi yêu cầu thêm/xóa cho mylist
	// type = movie/series, task = add/delete, id = movie_id/series_id
	function process_list(type, task, id)
	{
		$.ajax({
			url: "<?php echo base_url();?>index.php?browse/process_list/" + type + "/" + task + "/" + id, 
			success: function(result){
			//alert(result);
			if (task == 'add')
			{
				$("#mylist_button_holder").html( $("#mylist_delete_button").html() );
			}
			else if (task == 'delete')
			{
				$("#mylist_button_holder").html( $("#mylist_add_button").html() );
			}
		}});
	}
	
	// Hiển thị nút thêm/xóa danh sách yêu thích khi tải trang
	$( document ).ready(function() {
	
		// Kiểm tra xem movie_id này có tồn tại trong danh sách yêu thích của người dùng đang hoạt động không
		mylist_exist_status = "<?php echo $this->crud_model->get_mylist_exist_status('movie' , $featured_movie->movie_id);?>";
	
		if (mylist_exist_status == 'true')
		{
			$("#mylist_button_holder").html( $("#mylist_delete_button").html() );
		}
		else if (mylist_exist_status == 'false')
		{
			$("#mylist_button_holder").html( $("#mylist_add_button").html() );
		}
	});
</script>
<!-- DANH SÁCH CỦA TÔI, THỂ LOẠI DANH SÁCH WISE & SLIDER -->
<?php 
	$genres		=	$this->crud_model->get_genres();
	foreach ($genres as $row):
		// đếm số phim thuộc thể loại này, nếu không tìm thấy thì quay lại.
		$this->db->where('genre_id' , $row['genre_id']);
        $total_result = $this->db->count_all_results('movie');
        if ($total_result == 0)
        	continue;
	?>
<div class="row" style="margin:20px 60px;">
	<h4><?php echo $row['name'];?></h4>
	<div class="content">
		<div class="grid">
			<?php 
				$movies	= $this->crud_model->get_movies($row['genre_id'] , 5, 0);
				foreach ($movies as $row)
				{
					$title	=	$row['title'];
					$link	=	base_url().'index.php?browse/playmovie/'.$row['movie_id'];
					$thumb	=	$this->crud_model->get_thumb_url('movie' , $row['movie_id']);
					include 'thumb.php';
				}
				?>
		</div>
	</div>
</div>
<?php endforeach;?>

<hr style="border-top:1px solid #333; margin-top: 50px;">
<div class="container">
	<?php include 'footer.php';?>
</div>