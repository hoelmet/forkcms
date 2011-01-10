<?php

/**
 * This is the settings-action, it will display a form to set general blog settings
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendBlogSettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load form
		$this->loadForm();

		// validates the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the settings form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// init settings form
		$this->frm = new BackendForm('settings');

		// add fields for pagination
		$this->frm->addDropdown('overview_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'overview_num_items', 10));
		$this->frm->addDropdown('recent_articles_full_number_of_items', array_combine(range(1, 10), range(1, 10)), BackendModel::getModuleSetting($this->URL->getModule(), 'recent_articles_full_num_items', 5));
		$this->frm->addDropdown('recent_articles_list_number_of_items', array_combine(range(1, 10), range(1, 10)), BackendModel::getModuleSetting($this->URL->getModule(), 'recent_articles_list_num_items', 5));

		// add fields for spam
		$this->frm->addCheckbox('spamfilter', BackendModel::getModuleSetting($this->URL->getModule(), 'spamfilter', false));

		// no Akismet-key, so we can't enable spam-filter
		if(BackendModel::getModuleSetting('core', 'akismet_key') == '')
		{
			$this->frm->getField('spamfilter')->setAttribute('disabled', 'disabled');
			$this->tpl->assign('noAkismetKey', true);
		}

		// add fields for comments
		$this->frm->addCheckbox('allow_comments', BackendModel::getModuleSetting($this->URL->getModule(), 'allow_comments', false));
		$this->frm->addCheckbox('moderation', BackendModel::getModuleSetting($this->URL->getModule(), 'moderation', false));

		// add fields for notifications
		$this->frm->addCheckbox('notify_by_email_on_new_comment_to_moderate', BackendModel::getModuleSetting($this->URL->getModule(), 'notify_by_email_on_new_comment_to_moderate', false));
		$this->frm->addCheckbox('notify_by_email_on_new_comment', BackendModel::getModuleSetting($this->URL->getModule(), 'notify_by_email_on_new_comment', false));

		// add fields for SEO
		$this->frm->addCheckbox('ping_services', BackendModel::getModuleSetting($this->URL->getModule(), 'ping_services', false));

		// add fields for RSS
		$this->frm->addCheckbox('rss_meta', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_meta_'. BL::getWorkingLanguage(), true));
		$this->frm->addText('rss_title', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_title_'. BL::getWorkingLanguage()));
		$this->frm->addTextarea('rss_description', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_description_'. BL::getWorkingLanguage()));
		$this->frm->addText('feedburner_url', BackendModel::getModuleSetting($this->URL->getModule(), 'feedburner_url_'. BL::getWorkingLanguage()));
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// parse additional variables
		$this->tpl->assign('commentsRSSURL', SITE_URL . BackendModel::getURLForBlock($this->URL->getModule(), 'comments_rss'));
	}


	/**
	 * Validates the settings form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// form is submitted
		if($this->frm->isSubmitted())
		{
			// shorten fields
			$feedburnerURL = $this->frm->getField('feedburner_url');

			// validation
			$this->frm->getField('rss_title')->isFilled(BL::getError('FieldIsRequired'));

			// feedburner URL is set
			if($feedburnerURL->isFilled())
			{
				// check if http:// is set and add if necessary
				$feedburner = !strstr($feedburnerURL->getValue(), 'http://') ? 'http://'. $feedburnerURL->getValue() : $feedburnerURL->getValue();

				// check if feedburner URL is valid
				if(!SpoonFilter::isURL($feedburner)) $feedburnerURL->addError(BL::getError('InvalidURL'));
			}

			// init variable
			else $feedburner = null;

			// form is validated
			if($this->frm->isCorrect())
			{
				// set our settings
				BackendModel::setModuleSetting($this->URL->getModule(), 'overview_num_items', (int) $this->frm->getField('overview_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'recent_articles_full_num_items', (int) $this->frm->getField('recent_articles_full_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'recent_articles_list_num_items', (int) $this->frm->getField('recent_articles_list_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'spamfilter', (bool) $this->frm->getField('spamfilter')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'allow_comments', (bool) $this->frm->getField('allow_comments')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'moderation', (bool) $this->frm->getField('moderation')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'notify_by_email_on_new_comment_to_moderate', (bool) $this->frm->getField('notify_by_email_on_new_comment_to_moderate')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'notify_by_email_on_new_comment', (bool) $this->frm->getField('notify_by_email_on_new_comment')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'ping_services', (bool) $this->frm->getField('ping_services')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'rss_title_'. BL::getWorkingLanguage(), $this->frm->getField('rss_title')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'rss_description_'. BL::getWorkingLanguage(), $this->frm->getField('rss_description')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'rss_meta_'. BL::getWorkingLanguage(), $this->frm->getField('rss_meta')->getValue());
				if($feedburner !== null) BackendModel::setModuleSetting($this->URL->getModule(), 'feedburner_url_'. BL::getWorkingLanguage(), $feedburner);
				if(BackendModel::getModuleSetting('core', 'akismet_key') === null) BackendModel::setModuleSetting($this->URL->getModule(), 'spamfilter', false);

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') .'&report=saved');
			}
		}
	}
}

?>