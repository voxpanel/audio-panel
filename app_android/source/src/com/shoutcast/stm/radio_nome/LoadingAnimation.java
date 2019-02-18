package com.shoutcast.stm.radio_nome;

import android.view.animation.Animation;
import android.view.animation.TranslateAnimation;
import android.widget.ImageView;

/**
 * Created by User on 2014.07.05..
 */
public class LoadingAnimation {
    private static TranslateAnimation animation;
    private ImageView loading;

    public LoadingAnimation(ImageView loading) {
        this.loading = loading;
    }

    public static boolean hasEnded() {
        boolean status = true;
        try {
            status = animation.hasEnded();
        } catch (Exception e) {
            e.getMessage();
        }
        if (status) return true;
        else return false;
    }

    public void startAnimation() {
        animation = new TranslateAnimation(-50, 50, 0, 0);
        animation.setDuration(800);
        animation.setFillAfter(true);
        animation.setRepeatMode(Animation.REVERSE);
        animation.setRepeatCount(Animation.INFINITE);
        loading.startAnimation(animation);
    }

    public void clearAnimation() {
        loading.clearAnimation();
    }
}

