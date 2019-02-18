package com.shoutcast.stm.radio_nome;

import com.shoutcast.stm.radio_nome.R;

import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.support.v4.app.NotificationCompat;

/**
 * Created by Márk on 2015.02.08..
 */
public class NotificationPanel {

    private static NotificationManager nManager;
    private Context parent;
    private NotificationCompat.Builder nBuilder;

    public NotificationPanel(Context parent) {
        this.parent = parent;
    }

    public static void notificationCancel() {
        nManager.cancel(1);
    }

    public void showNotification(String radioName, boolean status) {
        // TODO Auto-generated constructor stub
        Intent intent;
        if(!status){
             intent = new Intent(parent, MainActivity.class);
            //Resume
             intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);}
        else
            intent = new Intent();

        PendingIntent pendingIntent = PendingIntent.getActivity(parent, 0, intent, PendingIntent.FLAG_ONE_SHOT);
        //------------------------------------------------
        nBuilder = new NotificationCompat.Builder(parent)
                .setContentTitle(radioName)
                .setContentText(MainScreen.getRadioListName().getText().toString())
                .setSmallIcon(R.drawable.ic_stat_transmission4)
                .setContentIntent(pendingIntent)
                .setWhen(0);

        nManager = (NotificationManager) parent.getSystemService(Context.NOTIFICATION_SERVICE);

        Notification noti = nBuilder.build();
        noti.flags |= Notification.FLAG_NO_CLEAR;

        nManager.notify(1, noti);
    }
}