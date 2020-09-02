<!-- FUNÇÃO DA PAGINAÇÃO -->

<?php

/**
 * 
 * @return array $total_posts, $current_page, $posts_per_page
 */
function totalItemsPagination()
{
  $posts_per_page = 20;

  $request_total = wp_remote_post('https://api.cartaoparceiro.com.br/graphql', [
    'headers' => [
      'Content-Type' => 'application/json',
    ],
    'body' => wp_json_encode([
      'query' => '
						{
							getAccrediteds(filter: {page: 0, items: 100000000}){
								items{
								id
							}
						}
					}
				'
    ])
  ]);
  $decode_total = json_decode($request_total['body'], true);

  $total_posts    = count($decode_total['data']['getAccrediteds']['items']);
  $pages_count    = ceil($total_posts / $posts_per_page);
  $current_page   = (isset($_GET['pg']) && $_GET['pg'] >= 0 && $_GET['pg'] <= $pages_count) ? $_GET['pg'] : 0;

  return array($pages_count, $current_page, $posts_per_page);
}

?>

<!-- PAGINAÇÃO HTML -->

<?php
$maxLinks  = 2;
$pages_count = totalItemsPagination()[0];
$current_page = totalItemsPagination()[1];

if ($pages_count >= 0) :
  $url = get_permalink(); ?>
  <nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
      <?php
      echo '<li class="page-item"><a class="page-link" href="' . $url . '?pg=1" aria-label="Previous"><i class="far fa-arrow-left"></i></a></li>';

      for ($i = $current_page - $maxLinks; $i <= $current_page - 1; $i++) :
        if ($i >= 1) :
          echo '<li><a class="page-link" href="' . $url . '?pg=' . $i . '">' . $i . '</a></li>';
        endif;
      endfor;

      echo '<li class="page-item active"><a class="page-link" href="' . $url . '?pg=' . $current_page . '"> ' . $current_page . '</a></li>';

      for ($i = $current_page + 1; $i <= $current_page + $maxLinks; $i++) :
        if ($i <= $pages_count) :
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?pg=' . $i . '">' . $i . '</a></li>';
        endif;
      endfor;

      echo '<li class="page-item"><a class="page-link" href="' . $url . '?pg=' . $pages_count . '" aria-label="Next"><i class="far fa-arrow-right"></i></a></li>';
      ?>
    </ul>
  </nav>
<?php endif; ?>