package com.nagarrakshak
 
import android.app.Application
import com.nagarrakshak.data.BackendClient
 
class NagarRakshakApp : Application() {
    override fun onCreate() {
        super.onCreate()
        BackendClient.init(this)
    }
}
