package com.shoutcast.stm.radio_nome;

import android.telephony.PhoneStateListener;
import android.telephony.TelephonyManager;

/**
 * Created by User on 2014.07.11..
 */
public class PhoneCallListener extends PhoneStateListener {

    private boolean notRunWhenStart = true;
    public static boolean message = false;


    public void onCallStateChanged(int state, String incomingNumber) {
        try {
            if (notRunWhenStart)
                notRunWhenStart = false;
            else {
                super.onCallStateChanged(state, incomingNumber);
                switch (state) {
                    case TelephonyManager.CALL_STATE_IDLE:
                        if (MusicPlayer.isStarted()) {
                                message = true;
                        }
                        break;
                    case TelephonyManager.CALL_STATE_OFFHOOK:
                        if (MusicPlayer.isStarted()) {
                            message = true;
                            MusicPlayer.stopMediaPlayer();
                        }
                        break;
                    case TelephonyManager.CALL_STATE_RINGING:
                        if (MusicPlayer.isStarted()) {
                            message = true;
                            MusicPlayer.stopMediaPlayer();
                        }
                        break;
                    default:
                        break;
                }
            }
        } catch (Exception e) {
            e.getMessage();
        }
    }
}