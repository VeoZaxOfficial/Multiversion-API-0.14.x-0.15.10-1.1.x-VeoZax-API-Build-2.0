VeoZax API - Your own Legacy PocketMine-MP API
==============================================
VeoZax API is a Legacy version of PocketMine-MP made to Act as "Cross-Play" between 0.14.x, 0.15.10, 1.1.x versions of Minecraft Pocket Edition Servers.
It's build to let Legacy 0.14.x & 0.15.10 & 1.1.x version Clients to connect and play on a Single server.

VZ API Development Started from: 22/03/2026

========================================
My Journey in Creating an API like this:
=====================================
It was a dream of mine to Create a Triple versioned Servers of McPE Alpha/Legacy.
Many players has been requesting to me for Create an API like this.
They wanted to play with friends on a single server with these version of Minecraft Pocket Edition: 0.14.x, 0.15.x, 1.1.x

Some players quitted from 0.14.x, 0.15.x and moved to 1.1.5 due to:
1. Their android devices is compatible with those versions of MCPE.
2. Lost interest in those MCPE versions and find comfort in 1.1.x version of MCPE.

Due to those reasons, The Craftsman/MasterCraft/MCPE (0.14.x - 0.15.10) community slowly started to Die.

At this point, I decided to Create an API with those 3 versions of Minecraft Pocket Edition and letting them play together on a single PocketMine-MP server.

================================================================
Multiversion API 0.14.x - 0.15.10 - 1.1.x (VeoZax API Build 2.0)
================================================================
This API is mainly built top of the API called MySoft.

=====================================
What is my Role in building this API?
=====================================
MySoft API supports versions from 0.15.x to 1.21.80
From that, only version bridge from 1.1.x to 1.1.7 was taken and rebuilt to create the VeoZax API.
0.14.x and 0.15.x are taken from it's original source code. Although MySoft had version 0.15.x support, it was completely incomplete and buggy.
It crashed constantly and made it difficult for other players from 0.15.x to play freely. So i had to update and build 0.15.x separately.
In addition to doing that, i also tried 0.14.x to add. That version was the only one missing from the API.
Due to many people's requests, I decided to build it there no matter what. thus, development of this Project began on March 22, 2026.
The first two or three days of building this API felt very difficult.
Every step i took led me to failure. However, I decided to move forward for the Craftsman/MCPE Legacy Community.
I spent the first month without sleep working on this API. Every mistake and success in this API is carefully crafted.
So after all the observations and experiments, the work on this API was almost completed on August 5, 2026

Below are some of the things I specicically mentioned about my works:

1. Added 0.14.x support (Login handle, Player handle, chat, Event handlers to make it stable enough to play with the 0.15.x and 1.1.x players)
2. Reworked on 0.15.x support to make it stable enough to play with the 0.14.x and 1.1.x players.
3. Fixed the issue where a player cant craft anything except wood to planks.
4. Fixed Few Light updates between 0.14.x and 0.15.x players.
5. Fixed Particle bugs which were causing inaccurate particles and effects showing to players when breaking something.
6. Fixed Crash caused by eating bug between 0.15.x and 1.1.x
7. Fixed Ghost Armour Bug on 0.15.x
8. Added Chest animations.
9. Added Command Guardian to prevent players with Hack clients from gaining Access to the server.
10. Added inbuilt World to World Teleport system.
11. Added autoload to Worlds.
12. Added Auto Restart Feature to Prevent quick overloads.
13. Barely fixed Block place bugs on 0.15 and 0.14
14. Adjusted KB 0.14.x and 0.15 for players.
15. Fixed Crouching/Sneaking Bug for both 0.14.x and 0.15.x
16. Fixed throwable items bug for both 0.14.x and 0.15.x
17. Xp pickup, Item drops will now feels smooth like butter.
18. Fixed /setworldspawn is not updating world's spawn point.
19. Fixed where the player is not getting Respawned on the new Spawn Point.
20. Added Armour Durability for 0.14.x
21. Fixed Water Draining time to smoother.
22. Fixed fishing logic.
23. Added Cool world Generators with biomes and mountains and structures.
24. Added VeoZax.yml to manage Administrators and Owners Access Permissions.
25. Updated NameTag with Version support to the players.
26. Added Transfer Server for 1.1.x players to Travel between Server to Server within the Server.
27. Fixed Skin Bug.

None of these fixes came easy. Every single one on that list was a night where something broke on one version the moment it started working on another.
0.14.x would desync the moment 0.15.x behaved, and then 1.1.x would throw a tantrum on top of that. Building for one version is hard enough,
building for three at once, and keeping all three happy at the same time, is a whole different beast. But I kept going, because I knew what this
meant to the Craftsman/MCPE Legacy Community. This wasn't just code to me, it was keeping something alive that a lot of people still love and miss.

======================
Why I'm Releasing This
======================
This API was never meant to just sit on my machine. The whole point of building a Multi-Version bridge like this was to let the Legacy MCPE community
keep playing together, no matter which version they're stuck on or which one they grew attached to. Keeping it closed off would go against everything
this Project stood for from day one. So I'm publishing it here, for free, for anyone who wants to run their own Legacy 0.14.x - 0.15.10 - 1.1.x server
and keep this community going.

====================================
Some more Info You need to Know
====================================
I want to be honest about this: this whole thing was built by me, alone. No team, no co-developers, no one sitting next to me
debugging at 3am. Every fix on that list above, every version conflict I had to untangle between 0.14.x, 0.15.x and 1.1.x, was
me by myself, for months.

What I did build on top of is open source code, which is different from being helped. PocketMine-MP is the core this whole server
software is built on, made by the PocketMine Team. And the original 1.1.x - 1.1.7 version bridge that I rebuilt and rewrote to make
the VeoZax API came from an existing open API called MySoft. That's why they're named here, it's a license and origin thing, not a
"they helped me" thing. Reading someone else's open source code and then rebuilding, fixing, and extending it alone into a working
triple-version bridge is still solo work. I just don't want to pretend the original source didn't exist either.

Everything from "Added 0.14.x support" down to "Fixed Skin Bug" up there, that's mine.

=========================
Heads Up Before You Run This
=========================
Since this is built and maintained by one person, expect bugs. Expect edge cases I haven't caught yet, some incompatibility
between the versions here and there, and the occasional error that only shows up once real players are on the server. I've
tested this as much as I can alone, but I can't catch everything a whole community running it will run into.

If you hit something broken, please report it, that helps a lot. And if you know your way around PocketMine-MP and want to
help develop this further, fix bugs, add features, or just keep it alive long term, I'd genuinely welcome that. This was
built solo, but it doesn't have to stay that way. Reach out on Discord if you want to get involved.

=====================================
Plugin Development for VeoZax API
=====================================
If you're building plugins for a server running the VeoZax API, they need to be written against PocketMine-MP 3.0.0 ALPHA.

You also need to declare the API in your plugin.yml, or the plugin will not load:

api: ["VeoZaxAPI"]

Without that line, VeoZax API will refuse to load your plugin, so don't skip it.

======================================================
Requirements & How to Run - Checkout my YouTube Video
======================================================
I made a full video tutorial on my YouTube channel walking through how to set up and run the VeoZax API. If you're new to this
or just want to see it working before you dive in, watch it here:

[Watch the Setup Tutorial](https://youtu.be/taupSR8oO40?si=hL2mkyajHHlnTHyY)

If you'd rather skip GitHub and grab a ready-to-use copy directly, you can also download the API via MediaFire:

[Download VeoZax API (MediaFire)](https://www.mediafire.com/file/khi8ddjvs18hyg0/Multiversion_API_0.14.x_-_0.15.10_-_1.1.x.zip/file)

============
License
============
This API is licensed under the GNU Lesser General Public License v3 (or later), same as the original PocketMine-MP.
Feel free to use it. If you modify or enhance this API, please publish your changes back to GitHub, open source.
Do not claim this API as privately owned, it's built to stay free for every Legacy 0.14.x - 0.15.10 - 1.1.x user.

=========
Community
=========
YouTube: @VeoZax
Discord: https://discord.gg/dCzgPYam2J
Website: https://info.veozax.xyz

=============
Final Words
=============
If you're reading this, you're probably about to run your own piece of the Craftsman/MCPE Legacy community. Take care of it.
If you find bugs I missed, or you improve something, don't sit on it, share it back. That's the whole reason this exists.

Thanks for sticking with Legacy MCPE. This one's for you.

By VeoZax
