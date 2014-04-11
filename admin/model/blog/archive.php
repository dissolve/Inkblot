<?php
class ModelBlogArchive extends Model {
	public function getArchives() {
        $query = $this->db->query("SELECT `year`, `month` FROM " . DATABASE . ".posts group by `YEAR`,`MONTH` ORDER BY `year` DESC, `month` DESC");
        $data = $query->rows;
        $data_array = array();

        $month_names = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

        foreach($data as $archive){
            $data_array[] = array(
                'name' => $month_names[$archive['month']] .' ' .$archive['year'],
                'permalink' => $this->url->link('blog/archive', 'year='.$archive['year'] . '&' .'month='.$archive['month'] , '')
            );
        }

		return $data_array;
	}


}
?>
