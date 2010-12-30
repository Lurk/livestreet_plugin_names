<?php

class PluginNames_ActionRegistration extends ActionRegistration{

	/**
	 * Показывает страничку регистрации и обрабатывает её
	 *
	 * @return unknown
	 */
	protected function EventIndex() {
		/**
		 * Если нажали кнопку "Зарегистрироваться"
		 */
        
        if (isPost('submit_register')) {
			//Проверяем  входные данные
			$bError=false;
			/**
			 * Проверка логина
			 */
			if (!PluginNames_CheckLogin(getRequest('login'),'login',3,30)) {
				$this->Message_AddError($this->Lang_Get('registration_login_error'),$this->Lang_Get('error'));
				$bError=true;
			}
			/**
			 * Проверка мыла
			 */
			if (!func_check(getRequest('mail'),'mail')) {
				$this->Message_AddError($this->Lang_Get('registration_mail_error'),$this->Lang_Get('error'));
				$bError=true;
			}
			/**
			 * Проверка пароля
			 */
			if (!func_check(getRequest('password'),'password',5)) {
				$this->Message_AddError($this->Lang_Get('registration_password_error'),$this->Lang_Get('error'));
				$bError=true;
			} elseif (getRequest('password')!=getRequest('password_confirm')) {
				$this->Message_AddError($this->Lang_Get('registration_password_error_different'),$this->Lang_Get('error'));
				$bError=true;
			}
			/**
			 * Проверка капчи(циферки с картинки)
			 */
			if (!isset($_SESSION['captcha_keystring']) or $_SESSION['captcha_keystring']!=strtolower(getRequest('captcha'))) {
				$this->Message_AddError($this->Lang_Get('registration_captcha_error'),$this->Lang_Get('error'));
				$bError=true;
			}
			/**
			 * А не занят ли логин?
			 */
			if ($this->User_GetUserByLogin(getRequest('login'))) {
				$this->Message_AddError($this->Lang_Get('registration_login_error_used'),$this->Lang_Get('error'));
				$bError=true;
			}

			/**
			 * А не занято ли мыло?
			 */
			if ($this->User_GetUserByMail(getRequest('mail'))) {
				$this->Message_AddError($this->Lang_Get('registration_mail_error_used'),$this->Lang_Get('error'));
				$bError=true;
			}
			/**
			 * Если всё то пробуем зарегить
			 */
			if (!$bError) {
				/**
				 * Создаем юзера
				 */
				$oUser=Engine::GetEntity('User');
				$oUser->setLogin(getRequest('login'));
				$oUser->setMail(getRequest('mail'));
				$oUser->setPassword(function_encrypt(getRequest('password')));
				$oUser->setDateRegister(date("Y-m-d H:i:s"));
				$oUser->setIpRegister(func_getIp());
				/**
				 * Если используется активация, то генерим код активации
				 */
				if (Config::Get('general.reg.activation')) {
					$oUser->setActivate(0);
					$oUser->setActivateKey(md5(func_generator().time()));
				} else {
					$oUser->setActivate(1);
					$oUser->setActivateKey(null);
				}
				/**
				 * Регистрируем
				 */
                if ($this->User_Add($oUser)) {

                    /**
					 * Убиваем каптчу
					 */
					unset($_SESSION['captcha_keystring']);
					/**
					 * Создаем персональный блог
					 */
					$this->Blog_CreatePersonalBlog($oUser);


					/**
					 * Если юзер зарегистрировался по приглашению то обновляем инвайт
					 */
					if (Config::Get('general.reg.invite') and $oInvite=$this->User_GetInviteByCode($this->GetInviteRegister())) {
						$oInvite->setUserToId($oUser->getId());
						$oInvite->setDateUsed(date("Y-m-d H:i:s"));
						$oInvite->setUsed(1);
						$this->User_UpdateInvite($oInvite);
					}
					/**
					 * Если стоит регистрация с активацией то проводим её
					 */
					if (Config::Get('general.reg.activation')) {
						/**
						 * Отправляем на мыло письмо о подтверждении регистрации
						 */
						$this->Notify_SendRegistrationActivate($oUser,getRequest('password'));
						Router::Location(Router::GetPath('registration').'confirm/');
					} else {
						$this->Notify_SendRegistration($oUser,getRequest('password'));
						$this->Viewer_Assign('bRefreshToHome',true);
						$oUser=$this->User_GetUserById($oUser->getId());
						$this->User_Authorization($oUser,false);
						$this->SetTemplateAction('ok');
						$this->DropInviteRegister();
					}
				} else {
					$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
					return Router::Action('error');
				}
			}
		}
	}

}
?>