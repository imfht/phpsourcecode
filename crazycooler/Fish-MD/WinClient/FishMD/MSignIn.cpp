/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:用户登录对话框
*/
/************************************************************************/
#include "MSignIn.h"
#include <windows.h>
#include <QHBoxLayout>
#include <QPushButton>
#include "MSqldb.h"
#include "utils/MIniFile.h"
#include <QDesktopServices>
#include <QPainter>

using namespace std;


MSignIn::MSignIn(QWidget *parent)
	: QDialog(parent)
{
	ui.setupUi(this);

	//去掉边框
	setWindowFlags(Qt::FramelessWindowHint);
	setWindowIcon(QIcon(":/FishMD/fish.icon"));

	QVBoxLayout *vlayout = new QVBoxLayout(this);

	QHBoxLayout *layout = new QHBoxLayout;
	QPushButton *optionButton = new QPushButton(this);
	QPushButton *minimizeButton = new QPushButton(this);
	QPushButton *closeButton = new QPushButton(this);

	optionButton->setObjectName("OptionButton");
	optionButton->setFocusPolicy(Qt::NoFocus);
	optionButton->setFixedSize(27, 23);
	optionButton->setCursor(Qt::PointingHandCursor);

	minimizeButton->setObjectName("MinimizeButton");
	minimizeButton->setFocusPolicy(Qt::NoFocus);
	minimizeButton->setFixedSize(27, 23);
	minimizeButton->setCursor(Qt::PointingHandCursor);

	closeButton->setObjectName("CloseButton");
	closeButton->setFocusPolicy(Qt::NoFocus);
	closeButton->setFixedSize(27, 23);
	closeButton->setCursor(Qt::PointingHandCursor);

	layout->addStretch();
	layout->addWidget(optionButton);
	layout->addWidget(minimizeButton);
	layout->addWidget(closeButton);
	layout->addSpacing(5);

	layout->setSpacing(0);
	layout->setContentsMargins(0, 0, 0, 0);

	vlayout->addLayout(layout);
	vlayout->addStretch();

	vlayout->setContentsMargins(0, 0, 0, 0);

	MSignIn *that = this;
	connect(minimizeButton, &QPushButton::clicked, [that]() {that->showMinimized(); });
	connect(closeButton, &QPushButton::clicked, [that]() {that->reject(); });

	init();
}

MSignIn::~MSignIn()
{
}


void MSignIn::init()
{
	connect(ui.btnSignIn, &QPushButton::clicked, this, &MSignIn::signIn);
	connect(ui.btnSignUp, &QPushButton::clicked, this, &MSignIn::signUp);
	
	MKVdb *kvDB = g_global->getKVDB();
	bool remeber = kvDB->getT("remeberPassword", false);
	ui.checkRemeber->setChecked(remeber);
	if (remeber)
	{
		QString name = QString::fromUtf8(kvDB->getT<std::string>("username","").c_str());
		ui.editName->setText(name);
		ui.editPassword->setText("******");
	}
}

//HTCAPTION 表示点击在标题栏中，可以进行拖拽
//m_nBorder 可以设置为5-8左右
bool MSignIn::nativeEvent(const QByteArray &eventType, void *message, long *result)
{
	Q_UNUSED(eventType)
		MSG *param = static_cast<MSG *>(message);
	switch (param->message)
	{
	case WM_NCHITTEST:
	{
		int nX = GET_X_LPARAM(param->lParam) - this->geometry().x();
		int nY = GET_Y_LPARAM(param->lParam) - this->geometry().y();

		if (childAt(nX, nY) == ui.label)
		{
			*result = HTCAPTION;
		}
		else
		{
			return QWidget::nativeEvent(eventType, message, result);
		}

		return true;
	}
	}
	return QWidget::nativeEvent(eventType, message, result);
}

void MSignIn::onLoginCallback(int status, const MResponseData &data)
{

	ui.btnSignIn->setDisabled(false);

	if (status == QNetworkReply::TimeoutError || status == QNetworkReply::ConnectionRefusedError)
	{
		ui.labelError->setText("网络错误");
		qInfo() << "sign in failed by network error";
		return;
	}

	if (data.request->url == "/refresh" && data.status != 0)
	{
		ui.editPassword->setText("");
		ui.labelError->setText("当前的token已经过期或无效");
		qInfo() << "sign in failed by token invalid";
		return;
	}

	if (data.status == 0)
	{
		QJsonObject root = data.root;
		MHttp *http = g_global->getHttp();
		http->setToken(root["token"].toString());

		MKVdb *kvDB = g_global->getKVDB();
		kvDB->setT("remeberPassword", ui.checkRemeber->isChecked());
		kvDB->setT("username", ui.editName->text().toUtf8().data());
		kvDB->setT("token", root["token"].toString().toUtf8().data());

		g_global->setUserName(ui.editName->text());

		qInfo() << "sign in by " << ui.editName->text();

		QDialog::accept();
		return;
	}
	else
	{
		ui.labelError->setText("账号或者密码错误");
		qInfo() << "sign in failed by user name or password error";
	}
}

void MSignIn::signIn()
{
	QString username = ui.editName->text();
	QString password = ui.editPassword->text();

	if (username.isEmpty())
	{
		ui.labelError->setText("请输入用户名");
		return;
	}

	if (password.isEmpty())
	{
		ui.labelError->setText("请输入密码");
		return;
	}

	MHttp *http = g_global->getHttp();

	MKVdb *kvDB = g_global->getKVDB();
	bool remeber = kvDB->getT("remeberPassword", false);
	QString name = QString::fromUtf8(kvDB->getT<std::string>("username", "").c_str());
	QString token = QString::fromUtf8(kvDB->getT<std::string>("token", "").c_str());


	if (ui.checkRemeber->isChecked() && remeber && username == name && password == "******")
	{
		QJsonObject param;
		param["token"] = token;
		http->post(
			"/refresh",
			param,
			std::bind(&MSignIn::onLoginCallback, this, std::placeholders::_1, std::placeholders::_2),
			false);
	}
	else
	{
		QJsonObject param;
		param["name"] = username;
		param["password"] = password;
		http->post(
			"/sign-in",
			param,
			std::bind(&MSignIn::onLoginCallback, this, std::placeholders::_1, std::placeholders::_2),
			false);
	}

	ui.btnSignIn->setDisabled(true);
}

void MSignIn::signUp()
{
	QString signUpUrl = QString::fromUtf8(g_global->getIniFile()->get<string>("net.signUpUrl", SIGN_UP_URL).c_str());
	QDesktopServices::openUrl(QUrl(signUpUrl));
}