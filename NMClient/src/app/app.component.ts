import { Component, OnInit } from '@angular/core';
import { FlightService } from './_services/flight.service';
import { AccessTokenResponse } from './_models/AccessTokenResponse';
import { environment } from 'src/environments/environment.development';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'NM Flights';
  accessToken: AccessTokenResponse = { access_token: "" };

  constructor(private _flightService: FlightService) { }

  async ngOnInit(): Promise<void> {
    this._flightService.getAccessToken().subscribe(x => {
      environment.access_token = x.access_token;
      this._flightService.setHttpOptions();
      this._flightService.getFlights().subscribe(y => console.log(y))
    });

    // this._flightService.getFlights().subscribe(x => {console.log(x)});
  }
}
