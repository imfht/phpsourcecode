/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:主界面tittle栏中，更新时会旋转的图标
*/
/************************************************************************/
#include "MSpinLabel.h"
#include <QTimerEvent>
#include <QPainter>

MSpinLabel::MSpinLabel(QWidget *parent)
	: QLabel(parent)
{
	m_nTimerId = 0;
	m_count = 0;
	m_pixmap = NULL;
	m_speed = 10;
	m_size = 0;
	m_delayCount = 0;
}

MSpinLabel::~MSpinLabel()
{
	stop();
	if (m_pixmap)
	{
		delete m_pixmap;
		m_pixmap = NULL;
	}
}


void MSpinLabel::timerEvent(QTimerEvent *event)
{
	if (m_nTimerId == event->timerId())
	{
		m_count += m_speed;
		m_count %= 360;
		update();
		m_delayCount -= 100;
		if (m_delayCount <= 0)
			stop();
	}
}

void MSpinLabel::setSpinImage(const QString &strPixmap, int size)
{
	if (m_pixmap)
	{
		delete m_pixmap;
	}

	m_pixmap = new QPixmap(strPixmap);
	m_size = size;
}

void MSpinLabel::setSpeed(int speed)
{
	m_speed = speed;
}

void MSpinLabel::paintEvent(QPaintEvent *event)
{
	//绘制程序的边框
	QPainter painter(this);
	painter.setRenderHint(QPainter::SmoothPixmapTransform, true);
	painter.translate(m_size/2, m_size/2);
	painter.rotate(m_count);
	painter.translate(-m_size/2, -m_size/2);
	painter.drawPixmap(QRect(0, 0, m_size, m_size), *m_pixmap);
}

void MSpinLabel::start(int delay)
{
	stop();
	m_delayCount = delay == 0 ? 0x7ffffff : delay;
	m_nTimerId = startTimer(100);
}

void MSpinLabel::stop()
{
	if (m_nTimerId != 0)
	{
		killTimer(m_nTimerId);
		m_nTimerId = 0;
	}
}