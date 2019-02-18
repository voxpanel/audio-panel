package com.cover;

import com.shoutcast.stm.radio_nome.R;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.BitmapDrawable;
import android.view.animation.Animation;
import android.view.animation.LinearInterpolator;
import android.view.animation.RotateAnimation;
import android.widget.FrameLayout;

/**
 * Created by markO on 2015. 08. 26..
 */
public class CoverGenerator {
    private UrlGenerator urlGenerator;
    private static CircleImageView cover;
    private static FrameLayout mainView;
    private static Context context;

    public CoverGenerator(CircleImageView cover, FrameLayout mainView, Context context) {
        this.cover = cover;
        this.mainView = mainView;
        this.context = context;
    }

    public void start(String title){
        urlGenerator = new UrlGenerator();
        urlGenerator.execute(title);
    }

    public void rotate(float degree) {
        RotateAnimation rotateAnim = new RotateAnimation(0.0f, degree,
                RotateAnimation.RELATIVE_TO_SELF, 0.5f,
                RotateAnimation.RELATIVE_TO_SELF, 0.5f);

        rotateAnim.setDuration(20000);
        rotateAnim.setInterpolator(new LinearInterpolator());
        rotateAnim.setRepeatCount(Animation.INFINITE);
        cover.startAnimation(rotateAnim);
    }

    public static void setCover(Bitmap bitmap){
        BitmapDrawable ob = new BitmapDrawable(context.getResources(), CoverBlur.fastblur(bitmap, 1, 50));
        cover.setImageBitmap(bitmap);
        mainView.setBackgroundDrawable(ob);
    }

    public static void setCover(int value){
        Bitmap bitmap = BitmapFactory.decodeResource(context.getResources(), R.drawable.radio_logo);
        BitmapDrawable ob = new BitmapDrawable(context.getResources(), CoverBlur.fastblur(bitmap, 1, 50));
        cover.setImageResource(value);
        mainView.setBackgroundDrawable(ob);
    }

    public static BitmapDrawable setStartCover(Context con){
        Bitmap bitmap = BitmapFactory.decodeResource(con.getResources(), R.drawable.radio_logo);
        BitmapDrawable ob = new BitmapDrawable(con.getResources(), CoverBlur.fastblur(bitmap, 1, 50));
        return ob;
    }
}
