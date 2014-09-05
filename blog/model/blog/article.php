<?php
class ModelBlogArticle extends Model {


    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full articles

	public function getArticle($article_id) {
        $this->load->model('blog/post');
        $article = $this->model_blog_post->getPost($article_id);
        $article['article_id'] = $article['post_id'];
        return $article;
	}

	public function getArticleByDayCount($year,$month, $day, $daycount) {
        $article = $this->cache->get('article.'. $year.'.'.$month.'.'.$day.'.'.$daycount);
        if(!$article){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE post_type='article' AND year = '". (int)$year . "' AND
                                                                                  month = '". (int)$month . "' AND
                                                                                  day = '". (int)$day . "' AND
                                                                                  daycount = '". (int)$daycount . "'");
            $article = $query->row;
            $this->load->model('blog/post');
            $article = array_merge($article, array(
                'article_id' => $article['post_id'],
                'permalink' => $this->url->link('blog/article', 'year='.$article['year']. '&' . 
                                                'month='.$article['month']. '&' . 
                                                'day='.$article['day']. '&' . 
                                                'daycount='.$article['daycount']. '&' . 
                                                'slug=' . $article['slug'], ''),
                'shortlink' => $this->short_url->link('blog/shortener', 'eid='.$this->model_blog_post->num_to_sxg($article['post_id']), '')
            ));
            $this->cache->set('article.'. $year.'.'.$month.'.'.$day.'.'.$daycount, $article);
        }
		return $article;
	}

	public function getRecentArticles($limit=10, $skip=0) {
        $data = $this->cache->get('articles.recent.'. $skip . '.'.  $limit);
        if(!$data){
            $data_array = array();
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='article' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            foreach($data as $article){
                $data_array[] = $this->getArticle($article['post_id']);
            }
            $this->cache->set('articles.recent.'. $skip . '.' .$limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getArticlesByCategory($category_id, $limit=20, $skip=0) {
        $data = $this->cache->get('articles.category.'. $category_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE post_type='article' AND category_id = '".(int)$category_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $article){
                $data_array[] = $this->getArticle($article['post_id']);
            }
            $this->cache->set('articles.category.'.$category_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getArticlesByAuthor($author_id, $limit=20, $skip=0) {
        $data = $this->cache->get('articles.author.'. $author_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='article' AND author_id = '".(int)$author_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $article){
                $data_array[] = $this->getArticle($article['post_id']);
            }
            $this->cache->set('articles.author.'.$author_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getArticlesByArchive($year, $month, $limit=20, $skip=0) {
        $data = $this->cache->get('articles.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='article' AND `year` = '".(int)$year."' AND `month` = '".(int)$month."'  ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $article){
                $data_array[] = $this->getArticle($article['post_id']);
            }
            $this->cache->set('articles.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}


    public function addWebmention($data, $webmention_id, $comment_data){
        if(isset($data['year']) && isset($data['month']) && isset($data['day']) && isset($data['daycount'])) {
            $article = $this->getArticleByDayCount($data['year'],$data['month'], $data['day'], $data['daycount']);

            switch($comment_data['type']) {
            case 'like':
                $this->db->query("INSERT INTO ". DATABASE.".likes SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                    ", post_id = ".(int)$article['article_id']);
                $like_id = $this->db->getLastId();
                $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_like_id = '".(int)$like_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                $this->cache->delete('likes');
                break;

            case 'reply':
                $this->db->query("INSERT INTO ". DATABASE.".comments SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                    ((isset($comment_data['text'])  && !empty($comment_data['text']))? ", body='".$comment_data['text']."'" : "") .
                    ((isset($comment_data['name'])  && !empty($comment_data['name']))? ", source_name='".$comment_data['name']."'" : "") .
                    ((isset($comment_data['published'])  && !empty($comment_data['published']))? ", `timestamp`='".$comment_data['published']."'" : ", `timestamp`=NOW()") .
                    ", post_id = ".(int)$article['article_id'] .", approved=1");
                $comment_id = $this->db->getLastId();
                $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_comment_id = '".(int)$comment_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                $this->cache->delete('comments');
                break;

            case 'rsvp':
            case 'repost':
            case 'mention':
            default:
                $this->db->query("INSERT INTO ". DATABASE.".mentions SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                    ", post_id = ".(int)$article['article_id'] .", parse_timestamp = NOW(), approved=1");
                $mention_id = $this->db->getLastId();
                $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_mention_id = '".(int)$mention_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                $this->cache->delete('mentions');
                break;
            }
        } else {
            throw new Exception('Cannot look up record');
            //throwing an exception will go back to calling script and run the generic add
        }
    }

}
