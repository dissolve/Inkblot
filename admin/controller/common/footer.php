<?php
class ControllerCommonFooter extends Controller {   
	public function index() {


		if (file_exists(DIR_SYSTEM . 'config/svn/svn.ver')) {
			$data['text_footer'] .= '.r' . trim(file_get_contents(DIR_SYSTEM . 'config/svn/svn.ver'));
		}

		return $this->load->view('common/footer.tpl', $data);
	}
}
